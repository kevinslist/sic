<?php

class collection_controller extends controller{
  function control(){
    $slug = app::next();
    switch($slug){
    case('scan'):
      $this->view($slug, collection_scanner::handle());
      break;
    default:
      print 'nothing cs';
      break;
    }
  }
  
  function view($view, $data=array()){
    print $view;
    switch($view){
      case('menu'):
        print app::read(LAYOUT_VIEW . 'mainmenu/collection/collection_scanner_menu.php', $data);
        break;
    }   
    
  }

}