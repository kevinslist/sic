<?php
   
class navigation_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      case('top_level'):
        require_once APP_MODEL . 'search/artist_search.php';
        $artist_list = artist_search::artist_menu_list(app::next());
        if(!empty($artist_list)){
          include LAYOUT_VIEW . 'navigation/artist_list_menu.php';
        }else{
          print '<div> -- empty -- </div>';
        }
        break;
      default:
        
        $this->css(LAYOUT_CSS . 'navigation.css');
        $this->js(APP_JS . 'navigation.js');
        $artist_sort_menu = app::read(LAYOUT_VIEW . 'navigation/artist_sort_menu.php');
        include LAYOUT_VIEW . 'navigation/navigation.php';
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