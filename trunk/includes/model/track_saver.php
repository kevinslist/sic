<?php
require_once APP_LIB . 'getid3/getid3/getid3.php';
require_once APP_MODEL . 'track_saver/mp3_saver.php';
define('SIC_TXXX_LABEL', 'user_defined_text');
define('SIC_ARTIST_DESC_BEGIN', '{sic-artist-info{');
define('SIC_ALBUM_COMPILATION_ID', '{sic-album-compilation-id{');
define('COMPILATION_ID_PREFIX', '__sic__');
define('ARTIST_DESCRIPTION_SEPERATOR', 'sic__sic');
define('SIC_TXXX_END', '}}');


class track_saver {
  static $getid3;
  var $track_path   = null;
  var $track_id     = 0;
  var $file_size    = 0;
  
  var $track_title    = '';
  var $mime_type      = null;
  var $file_type      = null;
  var $track_seconds  = null;
  var $track_length   = null;
  var $bit_rate       = null;
  var $sample_rate    = null;
  var $track_year     = null;
  var $artist         = array();
  var $album          = array();
  var $track_number   = null; // should be array();
  var $genre          = array();
  var $comments       = array();
  var $track_popm     = array();

  //System_Daemon::info('data: %s', var_export($d['fileformat'],true));
  ///System_Daemon::info('STEP_START: %s', self::$settings['status']);
  
  static public function update_from_file($track_id, $track_path){
    if( !isset(self::$getid3)){ self::$getid3 = new getID3; }
    
    $track = null;
    //System_Daemon::info('analyze: %s', $track_path);
    $data = self::$getid3->analyze($track_path);
    //getid3_lib::CopyTagsToComments($data);
      
    switch($data['fileformat']){
      
      case('mp3'):
        $track = new mp3_saver($track_id);
        break;
      
      default:
        // type unsupported
        break;
    }
    
    if(!empty($track)){
      $track->tag_data($data);
      //System_Daemon::info('TRACKDATA: %s', var_export($track,true));
      $track->save_to_db();
    }else{
      
      //self::save_unsupported($data['fileformat']);
    }
    
  }
  
  function save_to_db(){
    //System_Daemon::info('SAVE[%s] %s', $this->track_id, $this->track_path);
    //System_Daemon::info('INFO: %s', var_export($this,true));
    $this->save_track_info();
    $this->save_genre_info();
    $this->save_popm_info();
    $this->save_track_artists();
    $this->save_track_albums();
    $this->save_track_comments();
    
  }
  
  static function clean_non_ascii_chars($in){
    $out = preg_replace('/[^(\x20-\x7F)]*/','', $in);
    $out = trim($out);
    return $out;
  }
  
  
  function save_track_comments(){
    foreach($this->comments as $c){
      $ct = track_saver::clean_non_ascii_chars($c);
      
      if(!empty($ct)){
        db::exec('INSERT INTO track_comments (track_comment, track_id) VALUES (?,?)', array($ct, $this->track_id));
      }
    }
  }
  
  function save_track_albums(){
    $track_number = empty($this->track_number) ? null : (int)$this->track_number;
    //System_Daemon::info('TRACK NUMBER: %s', $track_number);
    
    foreach($this->album as $album_name => $aid){
      $album_name = track_saver::clean_non_ascii_chars($album_name);

      if(!empty($album_name)){
      
        if(preg_match('`^' . COMPILATION_ID_PREFIX . '`', $aid)){
          db::exec('INSERT INTO albums (album_name, album_compilation_id) VALUES (?,?)', array($album_name, $aid));
          db::exec('INSERT INTO track_album (album_id, track_id,track_number) 
                            VALUES ((SELECT album_id FROM albums WHERE album_name=? AND album_compilation_id=?),?,?)
                       ON DUPLICATE KEY UPDATE track_number=?', 
                  array($album_name, $aid, $this->track_id, $this->track_number, $this->track_number));

        }else{

          $p = preg_split('`' . ARTIST_DESCRIPTION_SEPERATOR . '`', $aid, 2, PREG_SPLIT_NO_EMPTY);
          $artist_name        = empty($p[0]) ? '' : $p[0];
          $artist_description = empty($p[1]) ? '' : $p[1];
          $artist_id = (int)db::val('SELECT artist_id FROM artists WHERE artist_name = ? AND artist_description=?', array($artist_name, $artist_description));

          if(empty($artist_id)){
            $artist_id = 0;
          }

          db::exec('INSERT INTO albums (album_name, album_artist_id) VALUES (?,?)', array($album_name, $artist_id));
          db::exec('INSERT INTO track_album (album_id, track_id,track_number) 
                        VALUES ((SELECT album_id FROM albums WHERE album_name=? AND album_artist_id=?),?,?)
                       ON DUPLICATE KEY UPDATE track_number=?', 
                  array($album_name, $artist_id, $this->track_id, $this->track_number, $this->track_number));

        }
      }
      
    }
  }
  
  function save_track_artists(){
    foreach($this->artist as $artist_name => $desc){
      
      $artist_name = track_saver::clean_non_ascii_chars($artist_name);
      if(!empty($artist_name)){
        db::exec('INSERT INTO artists (artist_name, artist_description) VALUES (?,?)', array($artist_name, $desc));
        db::exec('INSERT INTO track_artist (artist_id, track_id) VALUES ((SELECT artist_id FROM artists WHERE artist_name=? AND artist_description=?),?)', array($artist_name, $desc, $this->track_id));
      }
      
    }
  }
  
  function save_popm_info(){
    foreach($this->track_popm as $popm){
      db::exec('INSERT INTO track_popm (track_id, popm_email, popm_rating, popm_counter) VALUES (?,?,?,?)', 
              array($this->track_id, $popm['email'], $popm['rating'], $popm['counter']));
    }
  }
  
  function save_genre_info(){
    foreach($this->genre as $g){
      $genre = track_saver::clean_non_ascii_chars($g);
      if(!empty($genre)){
        db::exec('INSERT INTO genres (genre_name) VALUES (?)', $genre);
        db::exec('INSERT INTO track_genre (genre_id, track_id) VALUES ((SELECT genre_id FROM genres WHERE genre_name=?),?)', array($genre, $this->track_id));
      }
    }
  }
  
  function save_track_info(){
    $sql = 'UPDATE tracks SET track_title=?, mime_type=?, track_length=?, track_seconds=?,
               file_size=?, sample_rate=?, bit_rate=?, file_format=?, track_year=?        
              WHERE track_id=?';
    $params = array($this->track_title, $this->mime_type, $this->track_length, $this->track_seconds, 
        $this->file_size, $this->sample_rate,$this->bit_rate, $this->file_format,
        $this->track_year, $this->track_id);

    $r = db::exec($sql, $params);
    //System_Daemon::info('track_info saved: %s', var_export($params,true));
    
  }
  
  static function clean_file_name($n){
    $i = pathinfo($n);
    //System_Daemon::info('clean_file_name %s', $i);
    while(in_array($i['extension'], collection_scanner::$song_filter)){
      $n = substr($n, 0, (strlen($n)-(strlen($i['extension'])+1)));
      $i = pathinfo($n);
    }
    $n = ucwords($n);
    //System_Daemon::info('CLEAN OUT %s', $n);
    return $n;
  }
  
  
}

