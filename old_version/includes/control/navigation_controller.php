<?php

class navigation_controller extends controller {

  function __construct() {
    
  }

  function control() {
    switch (app::next()) {
      case('search'):
        $query = strtolower($_POST['q']);
        $tracks = track_search::navigation_collection_search($_POST['q']);
        $data = array();
        $artists = array();
        $albums = array();
        
        
        foreach ($tracks as $t) {
          $l          = empty($t['artist_name']) ? '' : strtoupper(substr(current($t['artist_name']), 0, 1));
          
          if(!preg_match('`[a-z0-9]`i', $l)){
            if(!empty($l)){
              $l = 'Other';
            }else{
              $l = 'Unkown';
            }
          }
          
          
          $artist     = isset($t['artist_name'])  ? current($t['artist_name']) : '';
          $artist_id  = isset($t['artist_id'])    ? current($t['artist_id']) : 0;
          $album      = isset($t['album_name'])   ? current($t['album_name']) : '';
          $album_id   = isset($t['album_id'])     ? current($t['album_id']) : 0;
          
          $artid      = 'art'.$artist_id;
          $albid      = 'alb'.$album_id;
        
          if(!isset($data[$l])){
            $data[$l] = array();
          }
          if(!isset($data[$l][$artid])){
            $data[$l][$artid] = array();
          }
          if(!isset($data[$l][$artid][$albid])){
            $data[$l][$artid][$albid] = array();
          }
          $data[$l][$artid][$albid]['t'.$t['track_id']] = $t;
        }

        include LAYOUT_VIEW . 'navigation/search_sort_menu.php';
        break;
      case('album_level'):

        $track_list = artist_search::track_menu_list(app::next());
        if (!empty($track_list)) {
          include LAYOUT_VIEW . 'navigation/track_list_menu.php';
        } else {
          print '<div> -- empty -- </div>';
        }
        break;
      case('artist_level'):
        $album_list = artist_search::album_menu_list(app::next());
        if (!empty($album_list['albums']) || !empty($album_list['tracks'])) {
          include LAYOUT_VIEW . 'navigation/album_list_menu.php';
        } else {
          print '<div> -- empty -- </div>';
        }
        break;
      case('top_level'):
        $artist_list = artist_search::artist_menu_list(app::next());
        if (!empty($artist_list)) {
          include LAYOUT_VIEW . 'navigation/artist_list_menu.php';
        } else {
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