<?php
 require_once APP_MODEL . 'search/track_search.php';
class playlist_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      default:
        
        $this->css(LAYOUT_CSS . 'playlist.css');
        $this->js(APP_JS . 'playlist.js');
        $tracks = track_search::random(420);
        //$tracks = tra(LAYOUT_VIEW . 'playlist/artist_sort_menu.php');
        include LAYOUT_VIEW . 'playlist/playlist.php';
        break;
    }
  }
}