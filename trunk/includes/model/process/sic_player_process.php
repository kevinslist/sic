<?php
define('SIC_PLAYER_STOPPED', 0);
define('SIC_PLAYER_PAUSED', 1);
define('SIC_PLAYER_PLAYING', 2);

class sic_player_process extends sic_process {
  static $web_socket        = null;
  static $sic_player_socket = null;
  static $played_by_username = '';
  static $playing_track_id  = 0;
  static $track_history_id  = 0;
  static $playing_status    = 0;
  static $playing_length    = 0;
  static $playing_time      = 0;
  static $playing_volume    = 0;
  static $playlist_id       = 0;
  
  static $playlist_played   = array();
  static $prev_history      = array();
  static $next_history      = array();

  function __construct(){
    parent::__construct();
    $this->process_id = 'sic_player';
  }

  static function quit_update() {
    print 'default sic player quit update';
  }
  
  static function check_song_finished(){
    $p = 0;
    
    if(self::$playing_length - self::$playing_time < 2){
      self::$web_socket->broadcast('song finished do something');
      $track_id = self::get_next_track_id();
      self::$web_socket->broadcast('auto next: ' . $track_id);
      if($track_id){
        $p = self::play_track($track_id);
        self::$track_history_id = track_saver::new_track_history($track_id, 20);
        $p = $track_id;
        
        $json = track_search::info((int)$track_id);
        self::$web_socket->broadcast($json);
      }
    }
    return $p;
  }
  
  static function get_prev_track_id(){
    $track_id = 0;
    if(count(self::$prev_history) > 1){
      self::$next_history[] = array_pop(self::$prev_history);
      //print 'DOprev_track, ADD NEXT HIS: ' . var_export(self::$next_history,true) . "\r";
      $track_id = array_pop(self::$prev_history);
    }
    return $track_id;
  }

  static function user_prev_track($playlist_id = 0, $sic_username='') {
    $p = null;
    //print 'user_prev_track: ' . var_export(self::$prev_history,true) . "\r";
    
    $track_id = self::get_prev_track_id();
    if($track_id){
      $p = self::play_track($track_id, $playlist_id, $sic_username);
      self::$track_history_id = track_saver::new_track_history($track_id, 50, $sic_username);
      $p = $track_id;
    }
    return $p;
  }
  
  static function get_next_track_id($playlist_id = 0){
    $track_id = 0;
    if(count(self::$next_history)){
      $track_id = array_pop(self::$next_history);
    }else{
      $track_id = playlist_search::get_next_track_id($playlist_id, self::$playing_track_id, self::$playlist_played, self::$played_by_username);
    }
    return $track_id;
  }

  static function user_next_track($playlist_id = 0, $sic_username='') {
    $p = null;
    //print 'user_prev_track: ' . var_export(self::$prev_history,true) . "\r";
    $track_id = self::get_next_track_id($playlist_id);
    if($track_id){
      $p = self::play_track($track_id, $playlist_id, $sic_username);
      self::$track_history_id = track_saver::new_track_history($track_id, 50, $sic_username);
      $p = $track_id;
    }
    return $p;
  }

  static function user_played_track($track_id, $sic_username='', $playlist_id=0) {
    $p = self::play_track($track_id, $playlist_id, $sic_username);
    if($p){
      self::$track_history_id = track_saver::new_track_history($track_id, 100, $sic_username);
      self::$next_history = array();
    }
    return $p;
  }
  
  static function add_playlist_played($playlist_id, $track_id){
    if($playlist_id && $track_id){
      if(!isset(self::$playlist_played[$playlist_id])){
        self::$playlist_played[$playlist_id] = array();
      }
      self::$playlist_played[$playlist_id][$track_id] = $track_id;
    }
  }

  static function play_track($track_id = 0, $playlist_id = 0, $sic_username='') {
    $p = 0;
    if (self::$sic_player_socket && $track_id) {
      $track_path = db::val('SELECT track_path FROM tracks WHERE track_id=?', $track_id);
      self::stop_playing();
      self::clear_playlist();
      self::add_track($track_path);
      self::$playing_track_id = $track_id;
      self::$playing_status   = SIC_PLAYER_PLAYING;
      self::$prev_history[] = $track_id;
      self::add_playlist_played($playlist_id, $track_id);
      if(!empty($sic_username)){
        self::$played_by_username = $sic_username;
      }
      $p = 1;
    }
    return $p;
  }

  static function toggle_track() {
    $toggle = '';
    $info = self::get_info();
    //print "\rINFO:" . $info . ":ENDINFOEND\r";

    if (!empty($info)) {
      $toggle = self::read('pause');
    }
    return $toggle;
  }

  static function get_info() {
    return self::read('info');
  }

  static function seek_track($sec) {
    self::read('seek ' . $sec);
  }

  static function add_track($path) {
    self::read('add ' . $path);
  }

  static function clear_playlist() {
    self::read('clear');
  }

  static function stop_playing() {
    self::read('stop');
  }

  static function get_time() {
    return self::read('get_time');
  }

  static function get_length() {
    return preg_replace('`[^0-9]`', '.', self::read('get_length'));
  }

  static function get_volume() {
    return preg_replace('`[^0-9]`', '.', self::read('volume'));
  }

  static function is_playing() {
    //return preg_replace('`[^0-9]`', '.', self::read('is_playing'));
    $info = self::get_info();
    if (empty($info)) {
      $playing = 0;
    } else {
      $playing = self::read('status', true);
    }
    return $playing;
  }

  static function player_status() {
    return self::read('status');
  }

  static function read($str, $status_check = false) {
    $res = '';
    if ($str) {
      fwrite(self::$sic_player_socket, $str . "\n");
    }
    $c = 0;
    do {
      $c++;
      $i = fread(self::$sic_player_socket, 80);
      if ($i !== false) {
        $r = str_replace("\n", "", $i);
        $r = str_replace("\r", "", $r);
        $r = str_replace(" ", "", $r);
        $res .= $r;
      }
      //print '>';
    } while (!self::end_with($res, '>') && $i !== false && $c < 20);
    if ($c == 20) {
      self::$sic_player_socket = false;
    }
    if ($status_check) {
      //print "\rSTATUSCHECK:" . $res . "\r";
      if (strrpos($res, 'stateplaying')) {
        $ret = SIC_PLAYER_PLAYING;
      } elseif (strrpos($res, 'statepaused')) {
        $ret = SIC_PLAYER_PAUSED;
      } else {
        $ret = SIC_PLAYER_STOPPED;
      }
    } else {
      $ret = preg_replace('`([^0-9])`', '', $res);
    }
    return $ret;
  }

  static function end_with($Haystack, $Needle) {
    // Recommended version, using strpos
    return strrpos($Haystack, $Needle) === strlen($Haystack) - strlen($Needle);
  }
  
  static function update_player_status(){
    
    if (self::$sic_player_socket) {
      self::$playing_status = (int) self::is_playing();
      
      if(self::$playing_status){
        self::$playing_time   = self::get_time();
        self::$playing_length = self::get_length();
        self::$playing_volume = self::get_volume();
        if(SIC_PLAYER_PLAYING == self::$playing_status){
          self::check_song_finished();
        }
      }
    }
  }

  static function get_player_status() {
    $status = array('action' => 'player_status');
    $status['is_playing'] = self::$playing_status;
    $status['has_prev']   = count(self::$prev_history);
    $status['has_next']   = count(self::$next_history);
    
    if($status['is_playing']) {
      $status['track_id'] = self::$playing_track_id;
      $status['time']     = self::$playing_time;
      $status['length']   = self::$playing_length;
      $status['volume']   = self::$playing_volume;
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
      $command = 'vlc --extraintf=rc --rc-host=' .
              $this->host . ':' .
              $this->port .
              ' -I dummy >> ' . APP_DIR . 'out/vlc.out 2>&1 & echo $!';

      // --rc-fake-tty
      //printf urlencode($command);

      exec($command, $op);
      $pid = (int) $op[0];
      $pstart = time();
      $p = array($this->process_id, $pid, 1, $pstart, $pid, 1, $pstart);
      $r = db::exec('INSERT INTO process_status (process_id, process_pid, process_status, process_started) 
                  VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE process_pid=?,process_status=?,process_started=?', $p);
    }
    $did_start = true;
    db::exec('UNLOCK TABLES');
    return $did_start;
  }

}
