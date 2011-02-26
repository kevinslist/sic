<?php
 
class process_controller extends controller{
  
  function __construct(){
    
  }
  
  function control(){
    $slug = app::next();
    switch($slug){
      case('quit'):
        sic_process::quit();
        break;
      case('run'):
        $process_id = app::next(). '_process';
        $process    = new $process_id;
        $process->run(); 
        break;
      case('start'):
        $process_id = app::next(). '_process';
        $process    = new $process_id;
        die(json_encode($process->start())); 
        break;
      default:
        print 'process_controller';
        break;
    }
  }

}