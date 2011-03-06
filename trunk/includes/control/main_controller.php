<?php

class main_controller extends controller{
  
  function control(){
    if(sic_client::name()){
      print app::read(LAYOUT_VIEW . 'layout.php');
    }else{
      if(!empty($_POST)){
        $success = sic_client::login();
      }
      print app::read(LAYOUT_VIEW . 'login.php');
    }
  }

}
