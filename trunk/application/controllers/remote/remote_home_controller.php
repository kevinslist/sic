<?php

class remote_home_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
    $this->page_title('the matrix');
  }
  public function index(){
    gefen_8x8_matrix::init();
    $this->render_page(kb::view('remote/remote-main-menu'));
  }
}