<?php

class rtl_433_controller extends my_controller {

  public function start($arg = NULL) {
    while(TRUE){
      print 'DONGLE(' . $arg . ') RUNNING...' . PHP_EOL;
      $sleep = (int)$arg + 1;
      sleep($sleep);
    }
  }
}