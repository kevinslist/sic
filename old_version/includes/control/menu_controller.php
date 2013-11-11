<?php

class menu_controller extends controller {

  function __construct() {
  }

  function control() {
    switch(app::next()){
      default:
        include LAYOUT_VIEW . 'mainmenu/menu.php';
        break;
    }
  }

}