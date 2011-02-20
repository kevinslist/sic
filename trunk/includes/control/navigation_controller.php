<?php
   
class navigation_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      default:
        
        $this->css(LAYOUT_CSS . 'navigation.css');
        $this->js(APP_JS . 'navigation.js');
        include LAYOUT_VIEW . 'navigation.php';
        break;
        /*
        $r = db::vals('SELECT artist_name FROM artists ORDER BY artist_name ASC');
        $a = array();
        foreach($r as $b){
          $l = strtolower(substr($b, 0, 1));
          $a[$l] = $l;
        }
        print '<div>'. json_encode($a) . '</div>';
         * 
         */
        break;
    }
  }
}