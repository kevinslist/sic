<?php

/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */

class cron_tasks_controller extends my_controller {

  static $special_signal_last_checked = 0;

  public function __construct() {
    parent::__construct();
    self::$special_signal_last_checked = microtime(true);
    gefen_8x8_matrix::init();
    itach::init();
    hue::init();
    denon::init();
  }

  public function index(){
    
    while(true){
      usleep(kb::config('CRON_TASKS_GLOBAL_USLEEP'));
      config_router::check_signal_queue();
      $ct = microtime(true);
      if( ($ct - self::$special_signal_last_checked) > 5){
        self::$special_signal_last_checked = $ct;
        //print 'cron tasks still running...' . PHP_EOL;
      }
      
    }
  }

}
