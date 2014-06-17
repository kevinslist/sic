<?php

class lights_home_controller extends my_controller {
  public function __construct() {
    parent::__construct();
  }
  public function index(){
    die('<div>KB: ' . kb_hue::get_ip() . '</div>');
  }
}