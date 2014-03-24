<?php

class header_controller extends my_controller {

  public function index() {
    $assets = array(kb::icss('header/header-global'), kb::iscript('header/header-global'));
    $vars = array('assets' => implode("\r\n", $assets));
    die(kb::view('layouts/header_layout', $vars));
  }

}