<?php

class mp3_saver extends track_saver {
  
  public function __construct($track_id){
    $this->track_id     = (int)$track_id;
    $this->file_format  = 'mp3';
  }
  
  public function tag_data($d){
    
    $this->track_path     = $d['filenamepath'];
    $this->mime_type      = $d['mime_type'];
    $this->file_size      = (int)$d['filesize'];
    $this->track_seconds  = (int)$d['playtime_seconds'];
    $this->track_length   = $d['playtime_string'];
    $this->bit_rate       = (int)$d['audio']['bitrate'];
    $this->sample_rate    = (int)$d['audio']['sample_rate'];
    
    $this->parse_id3v2($d);
    $this->parse_id3v1($d);
    if(empty($this->track_title)){
      $this->track_title = track_saver::clean_file_name($d['filename']);
    }
    //System_Daemon::info('data: %s', var_export($d,true));
  }
  
  public function parse_id3v2($d){
    if(!empty($d['id3v2'])){
      $i = $d['id3v2'];
      if($i['comments']['title']){
        $this->track_title = trim(current($i['comments']['title']));
      }
      if($i['comments']['artist']){
        //$this->artist = $i['comments']['artist'];
        $temp = $i['comments']['artist'];
        
        foreach($temp as $a){
          $desc = isset($i['comments'][SIC_TXXX_LABEL][SIC_ARTIST_DESC_BEGIN . $a . SIC_TXXX_END]) ? $i['comments'][SIC_TXXX_LABEL][SIC_ARTIST_DESC_BEGIN . $a . SIC_TXXX_END] : '';
          $this->artist[$a] = $desc;
        }
      }
      
      if($i['comments']['album']){
        //$this->album = $i['comments']['album'];
        $temp = $i['comments']['album'];
        if(count($this->artist)){
          $tk = current(array_keys($this->artist));
          $td = current($this->artist);
          $aid = $tk . ARTIST_DESCRIPTION_SEPERATOR . $td;
        }else{
          $aid = '';
        }
        
        foreach($temp as $a){
          $aid = isset($i['comments'][SIC_TXXX_LABEL][SIC_ALBUM_COMPILATION_ID . $a . SIC_TXXX_END]) ?  $i['comments'][SIC_TXXX_LABEL][SIC_ALBUM_COMPILATION_ID . $a . SIC_TXXX_END]: $aid;
          $this->album[$a] = $aid;
        }
      }    
      
      if($i['comments']['track_number']){
        $this->track_number = (int)current($i['comments']['track_number']);
      }
      if($i['comments']['recording_time']){
        $this->track_year = (int)current($i['comments']['recording_time']);
      }
      if($i['comments']['year']){
        $this->track_year = (int)current($i['comments']['year']);
      }
      
      if($i['comments']['genre']){
        $this->genre = $i['comments']['genre'];
        foreach($this->genre as $k=>$g){
          if(empty($g)){
            unset($this->genre[$k]);
          }
        }
      }
      if(!empty($i['comments']['comments'])){
        $this->comments = $i['comments']['comments'];
      }
  
      if($i['POPM']){
        foreach($i['POPM'] as $popm){
          $this->track_popm[$popm['email']] = array('email'=>trim($popm['email']), 'rating'=>(int)$popm['rating'], 'counter'=>(int)$popm['counter']);
        }
      }
  
      //System_Daemon::info('ID3v2: %s', var_export($i,true));
      
      //System_Daemon::info('ID3v2: %s', var_export($i,true));
      //System_Daemon::info('ALl: %s', var_export($d,true));
    }
  }
  
  public function parse_id3v1($d){
    if(!empty($d['id3v1'])){
      $i = $d['id3v1'];
      //System_Daemon::info('ID3v1: %s', var_export($i,true));
      if(empty($this->track_title)){
        $this->track_title = $i['title'];
      }
      if(empty($this->artist)){
        //$this->artist[] = $i['artist'];
        //$this->artist = $i['comments']['artist'];
        $temp = is_array($i['artist']) ? $i['artist'] : array();
        
        foreach($temp as $a){
          $this->artist[$a] = '';
        }
      }
      if(empty($this->album)){
        //$this->album[] = $i['album'];
        $temp = is_array($i['album']) ? $i['album'] : array();
        foreach($temp as $a){
          $this->album[$a] = '';
        }
      }
      if(empty($this->track_year)){
        $this->track_year = (int)$i['year'];
      }
      if(empty($this->genre)){
        $this->genre[] = $i['genre'];
      }
      if(empty($this->comments) && !empty($i['comment'])){
        $this->comments[$i['comment']] = trim($i['comment']);
      }
      if(empty($this->track_number) && !empty($i['track'])){
        $this->track_number = $i['track'];
      }
      
    }
  }
}

