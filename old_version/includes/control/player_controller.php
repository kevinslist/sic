<?php
 
class player_controller extends controller{
  function __construct(){
    
  }
  
  function control(){
    $slug = app::next();
    switch($slug){
      case('pause'):
        print 'p1';
        $track_id = (int)app::next();
        $this->view($slug, mplayer::pause());
        break;
      case('start'):
        $this->view($slug, mplayer::start());
        break;
      default:
        $this->view($slug, mplayer::menu($slug));
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
        print app::read(LAYOUT_VIEW . 'mplayer/mplayer_menu.php', $data);
        break;
    }   
    
  }
  
  function control2(){
    switch(app::next()){
      case('goto'):
        $track_id = (int)app::next();
        $r = db::row('SELECT * FROM tracks WHERE track_id = ?', $track_id);
        
        //$command = 'php ' . APP_SCRIPTS . 'collection_scanner_start.php';
        print '<div>' . $r['track_path'] . '</div>';
        $command = 'mplayer ' . $r['track_path'];
        //$r = shell_exec($command); 
        print 'play: ' . $track_id . '::' . $r;
        break;
      default:
        print 'here';
        die();
    }
  }
}