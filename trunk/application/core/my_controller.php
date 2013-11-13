<?php

if (!defined('BASEPATH')){  exit('No direct script access allowed'); }

class my_controller extends kb_controller {

  public function MY_Controller() {
    parent::__construct();
    $this->load->model('client');
    $this->client->init();
  }
}