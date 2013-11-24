<?php

class header_controller extends my_controller {

  public function index() {
    $assets = array(kb::icss('header/header-global'), kb::iscript('header/header-global'));
    $vars = array('assets' => implode("\r\n", $assets));
    kb::view('header/header_layout', $vars);
  }

}