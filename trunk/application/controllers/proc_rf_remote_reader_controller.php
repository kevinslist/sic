<?php

class proc_rf_remote_reader_controller extends my_controller {

  public function index() {
    $this->load->helper('process_rtl433');
    $d = dirname(dirname(__FILE__));
    $s = $d . '/third_party/kb/builds/rtl443/build/src/rtl_433 -a -D 2>&1';
    process_rtl433::$script_command = $s;
    process_rtl433::start();
    
  }

}