<?php

class lights_home_controller extends my_controller {
  
  public function __construct() {
    parent::__construct();
    $this->page_title('lights');
    hue::init();
  }
  public function index(){
    /*
    $r = hue::do_hue();
    $this->kb_content[] = implode("<br />\r\n", $r);
     * 
     */
    $this->render_page(kb::view('lights/lights-main-menu'));
  }
}