<?php

class collection_scanner_controller extends controller{
  
  function control(){
    $slug = app::next();
    switch($slug){
      case('start'):
        $this->view($slug, collection_scanner::start($slug));
        break;
      default:
        $this->view($slug, collection_scanner::menu($slug));
        break;
    }
  }
  
  function view($view, $data=array()){

    switch($view){
      case('start'):
        die(json_encode($data));
        break;
      case('menu'):
        //print_r($data);
        print app::read(LAYOUT_VIEW . 'mainmenu/collection/collection_scanner_menu.php', $data);
        break;
    }   
    
  }

}