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
  }

  public function index(){
    
    while(true){
      
      usleep(kb::config('CRON_TASKS_GLOBAL_USLEEP'));
      $current_time = microtime(true);
      $diff = $current_time - self::$special_signal_last_checked;
      if($diff > 1.8){
        self::$special_signal_last_checked = $current_time;
        config_router::process_special_buffer();
      }
      config_router::check_signal_queue();
      
    }
  }

}
