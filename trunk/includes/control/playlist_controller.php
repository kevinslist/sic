<?php
 
class playlist_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      default:
        
        $popm_default = settings::val('popm_email_default');
        print $popm_default;
        break;
    }
  }
}