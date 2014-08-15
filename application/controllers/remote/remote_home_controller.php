<?php

class remote_home_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
    $this->page_title('the matrix');
  }
  public function index($command){
    gefen_8x8_matrix::debug('index controller called:' . $command);
    //$this->render_page(kb::view('remote/remote-main-menu'));
  }
  
  public function route($input = NULL, $output = NULL){
    gefen_8x8_matrix::debug('route controller called:' . $input);
    gefen_8x8_matrix::route($input, $output);
  }
}