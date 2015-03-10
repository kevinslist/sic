<?php


class keep_alive_controller extends my_controller {

  static $special_signal_last_checked = 0;

  public function __construct() {
    parent::__construct();
    matrix::init();
  }

  public function index(){
    //$this->db->save_queries = FALSE;
    while(true){
      //usleep(kb::config('CRON_TASKS_GLOBAL_USLEEP'));
      //config_router::check_signal_queue();
    }
  }
  
  public function keep_alive_pi_station(){
    
  }

}
