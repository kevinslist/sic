<?php

/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */

class queue_controller extends my_controller {

  static $semaphore = null;

  public function __construct() {
    parent::__construct();
  }

  public function index($base_64_command = NULL) {

    $current_signal = unserialize(base64_decode(urldecode($base_64_command)));

    if (is_array($current_signal) && !empty($current_signal['remote-id']) && !empty($current_signal['signal-id']) && !empty($current_signal['last-signal'])) {
      self::_do_queue_signal($current_signal, $base_64_command);
    }
  }

  static function _do_queue_signal($current_signal = null, $base_64_command = null) {

    try {
      $key = kb::config('KB_CONFIG_ROUTER_INFO_SEM_LOCK_PORT');
      self::$semaphore = sem_get($key);
      $locked = sem_acquire(self::$semaphore);
      if ($locked) {
        $signal_queue_key = kb::config('KB_SIGNAL_QUEUE_KEY');
        $q = kb::mval($signal_queue_key);
        $q[md5($base_64_command)] = $current_signal;
        kb::mval($signal_queue_key, $q);
      }
    } catch (Exception $ex) {
      
    } finally {
      if (!empty(self::$semaphore)) {
        sem_release(self::$semaphore);
      }
    }
  }
}
