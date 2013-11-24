<?php

if (!defined('BASEPATH')){  exit('No direct script access allowed'); }

class my_controller extends kb_controller {

  public function __construct() {
    parent::__construct();
    $this->load->helper('url');
    $this->load->model('client');
  }
  public function render_page(){
    parent::render_page();
  }
}