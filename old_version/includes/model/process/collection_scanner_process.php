<?php

class collection_scanner_process extends sic_process {


  function __construct() {
    parent::__construct();
    $this->process_id = 'sic_socket';
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
  
}
