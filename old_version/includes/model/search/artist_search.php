<?php

class artist_search {

  static function artist_menu_list($letter) {
    $artists = array();

    if (preg_match('`^[A-Z]{1}$`', $letter)) {
      $artists = db::query('SELECT * FROM artists WHERE artist_name LIKE ? or artist_name LIKE ? ORDER BY artist_name ASC', array($letter . '%', strtolower($letter) . '%'));
    } elseif (preg_match('`^[0-9]{1}$`', $letter)) {
      $artists = db::query('SELECT * FROM artists WHERE artist_name LIKE ? ORDER BY artist_name ASC', array($letter . '%'));
    } elseif (preg_match('`unknown`i', $letter)) {
      $artists = self::album_menu_list(0);
    }else {
      $artists = db::query("SELECT * FROM artists WHERE artist_name REGEXP '^[^a-zA-Z0-9]' ORDER BY artist_name ASC");
    }

    return $artists;
  }
  
  static function album_menu_list($artist_id){
    $albums['albums'] = db::query('SELECT album_id, album_name FROM albums WHERE album_artist_id=? ORDER BY album_name ASC', (int)$artist_id);
    $albums['tracks'] = db::query('SELECT t.track_id, t.track_title
                                      FROM tracks t 
                                      LEFT JOIN track_artist tart ON (t.track_id = tart.track_id) 
                                      LEFT JOIN track_album ta ON t.track_id=ta.track_id
                                      WHERE tart.artist_id = ? AND ta.track_id IS NULL ORDER BY t.track_title ASC', (int)$artist_id);
    return $albums;
  }
  
  static function track_menu_list($album_id){
    return db::query('SELECT t.track_id, t.track_title
                                      FROM tracks t 
                                      LEFT JOIN track_album ta ON t.track_id=ta.track_id
                                      WHERE ta.album_id = ?', (int)$album_id);
  }
  
  
  static function build_album_level_menu_albums($a){
    return sprintf('<li class="album-level" data-album-id="%s">
              <div class="album-name drag-to-playlist" data-menu-type="album-level">%s</div>
              <div class="track-list-wrapper" id="%s">track list loading...</div>
            </li>', $a['album_id'], $a['album_name'], 'albid'.$a['album_id']);
  }

  static function build_album_level_menu_tracks($a){
    return sprintf('<li class="track-level" data-track-id="%s">
              <div class="track-name drag-to-playlist" data-menu-type="track-level">%s</div>
            </li>', $a['track_id'], $a['track_title'], 'trkid'.$a['track_id']);
  }

}
