<?php

class proc_rf_remote_reader_controller extends my_controller {

  public function index($arg) {

    $this->load->helper('process_rtl433');
    $d = dirname(dirname(__FILE__));
    $remote_codes = array(
        'a' => '433920000',
        'b' => '418920000',
    );
    $s = $d . '/third_party/kb/builds/rtl443/build/src/rtl_433 -a -D -f';
    process_rtl433::$script_command = $s;
    process_rtl433::$script_output = '2>&1';
    process_rtl433::$script_frequency = $remote_codes[$arg];
    process_rtl433::$script_remote = $arg;
    process_rtl433::start();
    
  }

}