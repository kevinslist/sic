<?php

class playlist_search {
  
  static function get_next_track_id($playlist_id = 0, $playing_track_id = 0, $playlist_played, $sic_username){
    $player_shuffle = settings::val('player_shuffle');
    $player_repeat  = settings::val('player_repeat');
    $track_id       = 0;
    
    if(!empty($played_by_username)){
      if(empty($playlist_id)){
        $playlist_history = db::row('SELECT * FROM playlist_history WHERE sic_username=? ORDER BY playlist_accessed DESC', $sic_username);
      }else{
        $playlist_history = db::row('SELECT * FROM playlist_history WHERE sic_username=? AND playlist_id = ? ORDER BY playlist_accessed DESC', 
                array($sic_username,$playlist_id));
      }
    }else{
      if(empty($playlist_id)){
        $playlist_history = db::row('SELECT * FROM playlist_history ORDER BY playlist_accessed DESC');
      }else{
        $playlist_history = db::row('SELECT * FROM playlist_history WHERE playlist_id = ? ORDER BY playlist_accessed DESC', $playlist_id);
      }
    }
    
    if(!empty($playlist_history)){
      
      $all_tracks = db::vals('SELECT track_id FROM playlist_tracks WHERE playlist_id=? 
                              ORDER BY playlist_track_order ASC', 
                              (int)$playlist_history['playlist_id']);
      $next_id = 0;
      $found_current = false;
      
      if(isset($playlist_history['playlist_id'])){
        $possible_tracks = array();
        foreach($all_tracks as $tid){
          if(!isset($playlist_history['playlist_id'][$tid])){
            $possible_tracks[] = $tid;
            if($found_current && empty($next_id)){
              $next_id = $tid;
            }
            if($playing_track_id == $tid){
              $found_current = true;
            }
          }
        }
      }else{
        $possible_tracks = $all_tracks;
      }
      
      $track_id = $next_id;
      // check random, order, etc...
      
    }
    
    if(empty($track_id)){
      print "GRAB RANDOM TRACK ID FROM tracks\r";
    }
    
    return $track_id;
  }

  static function load_playlist($playlist_id = 0) {
    
    if(empty($playlist_id)){
      // get users recent accessed playlist
      $playlist_id = db::val('SELECT playlist_id FROM playlist_history WHERE sic_username=? ORDER BY playlist_accessed DESC', sic_client::name());
    }else{
      // check if given pid is valid playlist
      $playlist_id = db::val('SELECT playlist_id FROM playlists WHERE playlist_id=?', (int)$playlist_id);
    }
    
    if(empty($playlist_id)){
      // no history or invalid - grab recently modified playlist
      $playlist_id = db::val('SELECT playlist_id FROM playlists ORDER BY playlist_modified DESC');
    }
    
    if(empty($playlist_id)){
      // if still empty create new playlist
      $playlist_id = self::create_new_user_playlist();
      self::add_random_tracks($playlist_id);
    }
    return $playlist_id;
  }
  
  static function get_playlist_tracks($playlist_id){
    self::update_playlist_history($playlist_id);
    return track_search::load_playlist($playlist_id);
  }
  
  static function add_random_tracks($playlist_id){
    $tracks = track_search::sort(track_search::random(), 'artist_name_index');
    $i = 1;
    foreach($tracks as $t){
      db::exec('INSERT INTO playlist_tracks (playlist_id, track_id, playlist_track_order) VALUES(?,?,?)',
       array($playlist_id, $t['track_id'], $i));
      $i++;
    }
  }
  
  static function update_playlist_history($playlist_id){
    $t = time();
    db::exec('INSERT INTO playlist_history (playlist_id, sic_username, playlist_accessed) VALUES(?,?,?) 
                ON DUPLICATE KEY UPDATE playlist_accessed=?', array($playlist_id, sic_client::name(), $t, $t));
  }
  
  static function create_new_user_playlist($playlist_name = 'default'){
      return self::create_new_playlist($playlist_name, 'user');
  }
  
  static function create_new_playlist($playlist_name = 'default', $playlist_type = 'user'){
    $t = time();
    db::exec('INSERT INTO playlists (playlist_name, playlist_type, playlist_modified, playlist_created_by, playlist_created_time)
              VALUES (?,?,?,?,?)', array($playlist_name, $playlist_type, $t, sic_client::name(), $t));
    
    return (int)db::val('SELECT LAST_INSERT_ID()');
  }

  

}