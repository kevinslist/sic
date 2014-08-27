<?php
/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */
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