<?php

class computer_home_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
    $this->page_title('computer');
  }
  public function index(){
    die('comp index');
  }
  
  public function on(){
    itach::process_tv_signal('80inch', 1, 7, 'tv_pip_on_off');
    redirect();
  }
}