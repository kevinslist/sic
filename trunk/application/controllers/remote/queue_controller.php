<?php

/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */

class queue_controller extends my_controller {

  static $semaphore = null;
  static $remote_command_inserted_time = null;

  public function __construct() {
    parent::__construct();
  }

  public function index($base_64_command = NULL) {

    $current_signal = unserialize(base64_decode(urldecode($base_64_command)));

    if (is_array($current_signal) && !empty($current_signal['remote_command_remote_id']) 
                                  && !empty($current_signal['remote_command_signal_id']) ) {
      self::_do_queue_signal($current_signal, $base_64_command);
    }else {
      print '<<< ! QUEUE SIGNAL RECEIVED INVALID >>>' . PHP_EOL;
    }
  }

  static function _do_queue_signal($s = null, $base_64_command = null) {
    self::$remote_command_inserted_time = time();
    $s['remote_command_inserted_time'] = self::$remote_command_inserted_time;
    kb::db_insert('remote_commands', $s);
  }
}
