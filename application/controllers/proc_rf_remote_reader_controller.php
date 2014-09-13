<?php

class proc_rf_remote_reader_controller extends my_controller {

  public function index($arg = NULL) {
    exec("pgrep rtl_433", $output);
    if(count($output)){
      foreach($output as $line){
        $kill = 'kill -9 ' . (int)$line;
        print $kill . PHP_EOL;
        exec("$kill");
        sleep(1);
      }
    }
    $this->load->helper('process_rtl433');
    $app_directory = dirname(dirname(__FILE__));
    process_rtl433::start($app_directory, $arg);
   
  }

}