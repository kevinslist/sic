<?php

class main_controller extends controller{
  
  function control(){
    
		print app::read(LAYOUT_VIEW . 'layout.php');
  }

}
