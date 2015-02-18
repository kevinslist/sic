<?php

class test_controller extends my_controller {

  public function index($arg = NULL) {
    // The worker will execute every X seconds:
    //print 'shell_exec: ' . shell_exec('stop kbrtl');
    $this->kill_rtl_433();
  }
  
  public function kill_rtl_433(){
    exec("pgrep rtl_433", $output);
    if(count($output)){
      foreach($output as $line){
        $kill = 'kill -9 ' . (int)$line;
        print $kill . PHP_EOL;
        //exec("$kill");
        //sleep(2);
      }
    }
  }
}