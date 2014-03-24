<?php

class sidebar_right_controller extends my_controller {

  public function index() {
    $assets = array(kb::icss('sidebar/right'), kb::iscript('sidebar/right'));
    $vars = array('assets' => implode("\r\n", $assets));
    die(kb::view('layouts/sidebar_right_layout', $vars));
  }

}