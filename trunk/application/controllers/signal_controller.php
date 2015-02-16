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
  static $signal_key_check_special_last = 0;
  static $memcache_obj = null;
  static $cache = array();

  public function __construct() {
    parent::__construct();
  }

  public function validate($base_64_command = NULL) {

    //print 'SIGNAL.INDEX.BASE64_ENCODED:' . $base_64_command;
    $c = unserialize(base64_decode(urldecode($base_64_command)));
    if (is_array($c)) {
      $last_sent_new = (int) $c['last-signal'];
      $valid_time = signal_controller::valid_signal_time($last_sent_new, $c['is-repeat']);
      if ($valid_time) {
        $c['signal-name'] = config_channel::valid_remote_code($c['remote-string']);
        if ($c['signal-name']) {
          config_router::route($c);
        }
      }
    } else {
      print '<<< ! SIGNAL RECEIVED INVALID >>>' . PHP_EOL;
    }
  }

  public function check_special() {
    $current_check_special_time = microtime(true);
    $last_sent_check_special = (float) kb::pval(signal_controller::$signal_key_check_special_last);
    if (0 >= $last_sent_check_special) {
      // firsttime
      kb::pval(signal_controller::$signal_key_check_special_last, $current_check_special_time);
      $this->log('SERVER. SIGNAL. CHECK_Special:INIT' . $last_sent_check_special . ':::' . $current_check_special_time);
    } else {
      $diff = $current_check_special_time - $last_sent_check_special;
      if ($diff > 3.9) {
        //$this->log('SERVER. SIGNAL. CHECK_Special:' . $diff);
        kb::pval(signal_controller::$signal_key_check_special_last, $current_check_special_time);
      }
    }
  }

  static function valid_signal_time($last_sent_new = 0, $repeat = false) {
    $key = self::$signal_last_sent_key .= ($repeat ? '_repeat' : '_full');
    $last_sent_old = (int) kb::pval($key);
    return $last_sent_new > $last_sent_old + 3;
  }

  static function do_send_signal($signal = null) {
    $current_time = (int) $signal['last-signal'];
    $remote_code = '#' . $signal['header-string'];
    $current_signal = $signal['remote-string'];

    if (!$signal['is-repeat']) {
      itach::$remotes[$remote_code]['repeat'] = 0;
      itach::$remotes[$remote_code]['previous-signal'] = $current_signal;
      itach::$remotes[$remote_code]['last-sent'] = $current_time;
      itach::send_signal($remote_code, $signal['signal-name']);
    } else {
      print 'DONT SEND REPEAT RIGHT NOW>>>' . PHP_EOL;
    }
    //itach::send_signal($remote_code, $signal_name);
  }

}
