<?php

/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */

class signal_controller extends my_controller {

  static $signal_last_sent = 0;
  static $received_signal;
  static $signal_last_sent_key = 'kb_signal_last_sent';
  static $signal_key_check_special_last = 'kb_signal_key_check_special_last';
  static $memcache_obj = null;
  static $cache = array();
  var $current_signal;
  static $sem_key_validate = '123321';
  static $sem_key_check_special = '123322';
  static $sem_max = '1';
  static $sem_permissions = 0666;
  static $sem_auto_release = 1;

  public function __construct() {
    parent::__construct();
  }

  public function validate($base_64_command = NULL) {

    //print 'SIGNAL.INDEX.BASE64_ENCODED:' . $base_64_command;
    $this->current_signal = unserialize(base64_decode(urldecode($base_64_command)));
    if (is_array($this->current_signal) && !empty($this->current_signal)) {
      try {
        $semaphore = sem_get(signal_controller::$sem_key_validate, signal_controller::$sem_max, signal_controller::$sem_permissions, signal_controller::$sem_auto_release);
        //$this->log('Attempting to acquire semaphore');
        sem_acquire($semaphore);
        $valid_time = signal_controller::valid_signal_time();

        if ($valid_time) {
          $this->current_signal['signal-name'] = config_channel::valid_remote_code($this->current_signal['remote-string']);
          if ($this->current_signal['signal-name']) {
            $key = self::$signal_last_sent_key .= ($this->current_signal['is-repeat'] ? '_repeat' : '_full');
            $last_sent_old = (int) kb::pval($key, $valid_time);
            config_router::route($this->current_signal);
          }
        }
      } catch (Exception $ex) {
        $this->log('EXCEPTION IN VALIDATE:');
        $this->log(ex);
      } finally {
        sem_release($semaphore);
      }
    } else {
      print '<<< ! SIGNAL RECEIVED INVALID >>>' . PHP_EOL;
    }
  }

  public function check_special() {
    $current_check_special_time = microtime(true);
    //$this->log('check_special>getlast>:');
    
    try{
      $semaphore = sem_get(signal_controller::$sem_key_check_special, signal_controller::$sem_max, signal_controller::$sem_permissions, signal_controller::$sem_auto_release);
      //$this->log('Attempting to acquire semaphore_check_special');
      sem_acquire($semaphore);
      $last_sent_check_special = (float) kb::pval(signal_controller::$signal_key_check_special_last);
      
      if (0 >= $last_sent_check_special) {
        // firsttime
        kb::pval(signal_controller::$signal_key_check_special_last, $current_check_special_time);
        $this->log('SERVER. SIGNAL. CHECK_Special:INIT' . $last_sent_check_special . ':::' . $current_check_special_time);
      } else {
        $diff = $current_check_special_time - $last_sent_check_special;
        if ($diff > 2.7) {
          //$this->log('SERVER. SIGNAL. CHECK_Special:' . $diff);
          kb::pval(signal_controller::$signal_key_check_special_last, $current_check_special_time);
          config_router::route_special();
        }
      }
      
    } catch (Exception $ex) {
        $this->log('EXCEPTION IN check_special:');
        $this->log(ex);
    } finally {
      sem_release($semaphore);
    }
    
    
  }

  public function valid_signal_time() {
    $do_return = false;
    if (!empty($this->current_signal)) {
      $last_sent_new = (int) $this->current_signal['last-signal'];
      $key_full = self::$signal_last_sent_key . '_full';
      $last_sent_old = (int) kb::pval($key_full);
      $this->current_signal['last-sent'] = $last_sent_old;

      $diff = $last_sent_new - $last_sent_old;
      $diff = $last_sent_new - $last_sent_old;
      $this->current_signal['diff-last-full'] = $diff;
      $this->current_signal['valid-time'] = $diff > 3;

      if ($this->current_signal['valid-time'] && $this->current_signal['is-repeat']) {
        $key_repeat = self::$signal_last_sent_key . '_repeat';
        $last_sent_old = (int) kb::pval($key_repeat);
        $diff = $last_sent_new - $last_sent_old;
        $diff = $last_sent_new - $last_sent_old;
        $this->current_signal['diff-last-repeat'] = $diff;
        $this->current_signal['valid-time'] = $diff > 3;
      }
      $do_return = $this->current_signal['valid-time'] ? $last_sent_new : false;
    }
    return $do_return;
  }

}
