<?php

class sic_player_process extends websocket {

  static $sic_player_socket = null;
  static $playing_track_id  = 0;
  var $running_status = 1;
  
  static function play_track($track_id){
    $p = 0;
    if(self::$sic_player_socket && $track_id){
      $track_path = db::val('SELECT track_path FROM tracks WHERE track_id=?', $track_id);
      self::stop_playing();
      self::clear_playlist();
      self::add_track($track_path);
      self::$playing_track_id = $track_id;
      $p = 1;
      //print 'PLAY TRACK: ' . $track_path . "\n";
    }
    return $p;
  }
  
  static function add_track($path){
    self::read('add ' . $path);
  }
  
  static function clear_playlist(){
    self::read('clear');
  }
  
  static function stop_playing(){
    self::read('stop');
  }
  static function get_time(){
    return self::read('get_time');
  }
  static function get_length(){
    return preg_replace('`[^0-9]`', '.', self::read('get_length'));
  }
  static function get_volume(){
    return preg_replace('`[^0-9]`', '.', self::read('volume'));
  }

  static function is_playing() {
    //return preg_replace('`[^0-9]`', '.', self::read('is_playing'));
    return self::read('is_playing');
  }
  static function player_status() {
    return self::read('status');
  }

  static function read($str) {
    $res  = '';
    if($str){
      fwrite(self::$sic_player_socket, $str . "\n");
    }
    do{
      $r = fread(self::$sic_player_socket, 80);
      $r = str_replace("\n", "", $r);
      $r = str_replace("\r", "", $r);
      $r = str_replace(" ", "", $r);
      $res    .= $r;
    }while(!self::end_with($res, '>'));
    
    return preg_replace('`([^0-9])`', '', $res);
  }
  
  static function end_with($Haystack, $Needle){
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack)-strlen($Needle);
  }

  static function get_player_status() {
    $status = '';
    if(self::$sic_player_socket){
      $t = time();
      $status = array('action' => 'player_status');
      $status['is_playing']     = (int)self::is_playing();
      if($status['is_playing']){
        $status['track_id'] = self::$playing_track_id;
        $status['time']       = self::get_time();
        $status['length']   = self::get_length();
        $status['volume']   = self::get_volume();
      }
    }
    return $status;
  }

  function process($user, $msg) {
    
  }

  function run() {
    $this->port = settings::val('sic_player_port');
    $this->host = settings::val('sic_player_host');
    return $this->start_player();
  }

  function start_player() {
    $did_start = false;
    db::exec('LOCK TABLE process_status WRITE');
    if (!$this->running()) {
      //$command = 'nohup php ' . APP_DIR . 'index.php /process/run/' . $this->process_id . ' > ' . $this->log_file . ' 2>&1 & echo $!';
      $command = 'cvlc --extraintf=rc --rc-host=' . 
              $this->host . ':' . 
              $this->port . 
              ' -I dummy >/var/www/vlc_out 2>&1 & echo $!';
      
                // --rc-fake-tty
      //printf urlencode($command);
      
      exec($command, $op);
      $pid = (int) $op[0];
      $pstart = time();
      $p = array($this->process_id, $pid, $this->running_status, $pstart, $pid, $this->running_status, $pstart);
      $r = db::exec('INSERT INTO process_status (process_id, process_pid, process_status, process_started) 
                  VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE process_pid=?,process_status=?,process_started=?', $p);
    }
    $did_start = true;
    db::exec('UNLOCK TABLES');
    return $did_start;
  }

  function __construct() {
    $this->process_id = 'sic_player';
  }

}
