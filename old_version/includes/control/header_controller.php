<?php
 
class header_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      default:
        $this->css(LAYOUT_CSS . 'header.css');
        $this->js(APP_JS . 'header.js');
        include LAYOUT_VIEW . 'topnav.php';
        break;
    }
  }
}