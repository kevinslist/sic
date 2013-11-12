<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class default_controller extends my_controller {

	public function index()
	{
    die('kb');
	}
  
  public function error_404() {
    $this->index();
  }

  public function error_403() {
    $this->index();
  }

}