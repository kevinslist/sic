<?php
 
class sic_socket_controller extends controller{
  function __construct(){
    
  }
  
  function control(){
    $slug = app::next();
    switch($slug){
      case('init'):
        die(json_encode(sic_socket::init())); 
        break;
      default:
        print 'default-sic-controller';
        break;
    }
  }
  
  function view($view, $data=array()){

    switch($view){
      case('start'):
        die(json_encode($data));
        break;
      case('goto'):
      case('menu'):
        //print_r($data);
        break;
    }   
    
  }

}