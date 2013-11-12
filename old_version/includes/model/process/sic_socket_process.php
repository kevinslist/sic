<?php

class sic_socket_process extends websocket {

  var $running_status         = 1;
  var $sic_player_socket      = null;
  var $send_update_count      = 0;
  var $send_update_count_fix  = 0;

  static function quit_update(){
    print 'default sic socket quit update';
  }
  
  function update_status($user_action = false) {
    sic_player_process::update_player_status();
    if ($this->player_socket_connected()) {
      if ($user_action || $this->send_update_count > 0 || $this->send_update_count_fix > 9 && count($this->users)) {
        $this->send_update_count_fix = 0;
        $this->send_update_count--;

        if ($this->sic_player_socket) {
          $status = sic_player_process::get_player_status();
        } else {
          $status = array('action' => 'player_status', 'is_running' => 'false', 'connected' => 'false');
        }
        $this->broadcast($status);
        
      }else{
        $this->send_update_count_fix++;
      }
    }
  }

  function player_socket_connected() {
    $connected = false;
    if (empty(sic_player_process::$sic_player_socket)) {
      print 'EMPTY player socket';
      $connected = $this->connect_player_socket();
    } else {
      $connected = true;
    }
    return $connected;
  }

  function connect_player_socket() {
    $did_connect = false;
    $sic_player_port = settings::val('sic_player_port');
    $sic_player_host = settings::val('sic_player_host');
    print 'TRY CONNECT TO PLAYER: ' . time() . "\n";
    $this->sic_player_socket = fsockopen($sic_player_host, $sic_player_port, $errno, $errstr, 10);

    if (!$this->sic_player_socket) {
      echo "$errstr ($errno)\n";
      $this->sic_player_socket = null;
    } else {
      print 'DID SUCCESS CONNECT TO PLAYER: ' . time() . "\n";
      $did_connect = true;
      stream_set_timeout($this->sic_player_socket, 1);
      sic_player_process::$sic_player_socket = $this->sic_player_socket;
      //sic_player_process::read(false);
      print 'DID2: ' . time() . "\n";
    }
    return $did_connect;
  }

  function process($user, $msg) {
    parse_str($msg);
    if (isset($action) && $this->player_socket_connected()) {
      $this->log('PROCESS: %s', $action . '::' . $data);

      switch ($action) {
        case('toggle'):
          $json = sic_player_process::toggle_track();
          $this->send_update_count = 2;
          break;
        case('seek'):
          $json = sic_player_process::seek_track((int) $data);
          $this->send_update_count = 2;
          break;
        case('prev_track'):
          $json = sic_player_process::user_prev_track((int) $data, $sic_username);
          if(!empty($json)){
            $this->send_update_count = 6;
            $json = track_search::info($json);
          }
          break;
        case('next_track'):
          $json = sic_player_process::user_next_track((int) $data, $sic_username);
          if(!empty($json)){
            $this->send_update_count = 6;
            $json = track_search::info($json);
          }
          break;
        case('play'):
          $json = sic_player_process::user_played_track((int) $data, $sic_username, $playlist_id);
          $this->send_update_count = 6;
        case('track_info'):
          $json = track_search::info((int) $data);
          break;
        case('status'):
          $user->requested_status = true;
          $this->send_update_count = 1;
        default:
          $json = 'default';
          break;
      }
      if (!empty($json)) {
        $this->update_status();
        $this->broadcast($json);
      }
    } else {
      $action = isset($action) ? $action : ' - action not set(' . $msg . ') - ';
      print 'player socket not connected: ' . $action;
    }
  }

  function run() {
    $this->host = settings::val('sic_socket_host');
    $this->port = settings::val('sic_socket_port');

    $sic_player_process = new sic_player_process();
    $sic_player_process->run();
    if ($this->server_can_start()) {
      // start_server in child websocket class
      sic_player_process::$web_socket = $this;
      $this->start_server();
    }
  }

  function server_can_start() {
    $can_start = false;
    db::exec('LOCK TABLE process_status WRITE');
    if (!$this->running()) {
      $pid = getmypid();
      $pstart = time();

      $p = array($this->process_id, $pid, $this->running_status, $pstart, $pid, $this->running_status, $pstart);

      $r = db::exec('INSERT INTO process_status (process_id, process_pid, process_status, process_started) 
                  VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE process_pid=?,process_status=?,process_started=?', $p);
    }
    $can_start = true;
    db::exec('UNLOCK TABLES');
    return $can_start;
  }

  function __construct() {
    parent::__construct();
    $this->process_id = 'sic_socket';
  }
  


  function broadcast($data) {
    if (!empty($data)) {
      parent::broadcast(json_encode($data));
    }
  }

}