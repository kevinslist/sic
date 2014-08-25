<?php

class itach_home_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
    $this->page_title('itach rest');
  }
  public function index($get_command = NULL){
    itach::init($get_command);
    //$this->js_files[] = '/assets/kb/js/itach/itach_learn.js';
    //$this->render_page(kb::view('itach/itach-main-menu'));
    return;
    
    /*
    $r = hue::do_hue();
    $this->kb_content[] = implode("<br />\r\n", $r);
     * 
     */
    //print 'itach: ' . itach::init($get_command);
  }
}