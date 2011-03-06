<?php

class playlist_search {

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