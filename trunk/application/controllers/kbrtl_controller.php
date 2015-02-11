<?php

class kbrtl_controller extends my_controller {

  public function index($arg = NULL) {
    $this->kill_rtl_433();
    sleep(1);
    $this->load->helper('process_kbrtl');
    $app_directory = dirname(dirname(__FILE__));
    process_rtl433::start($app_directory, $arg);
   
  }
  
  public function kill_rtl_433(){
    exec("pgrep rtl_433", $output);
    if(count($output)){
      foreach($output as $line){
        $kill = 'kill -9 ' . (int)$line;
        print $kill . PHP_EOL;
        exec("$kill");
        sleep(2);
      }
    }
  }

}