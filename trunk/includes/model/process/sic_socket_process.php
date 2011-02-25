<?php

class sic_socket_process extends websocket {
  var $running_status = 1;

  function update_status(){
    //player::get_status();
    //System_Daemon::info('update_status');
    $this->broadcast( array('action'=>'status') );
  }
  
  function run(){
     // db::$force_new = true;
    print $this->process_id . ' running';
    $this->host = settings::val('sic_socket_host');
    $this->port = settings::val('sic_socket_port');

    if($this->server_can_start()){
      $this->start_server();
    }
  }
  
  function server_can_start(){
    $started = false;
    db::exec('LOCK TABLE process_status WRITE');
    if(!$this->running()){
      print 'PORT: ' . $this->port;
      print '::HOST: ' . $this->host;
      $pid      = getmypid();
      print '::PID: ' . $pid;
      $pstart  = time();
      
      //db::exec('INSERT INTO process_status (process_id, process_pid, process_status, process_started) 
      //  VALUES (?, ?, ?, ?)', array($this->process_id, $pid, $this->running_status, $pstart));
      
     $p = array($this->process_id, $pid, $this->running_status, $pstart, $pid, $this->running_status, $pstart);
      
      $r = db::exec('INSERT INTO process_status (process_id, process_pid, process_status, process_started) 
                  VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE process_pid=?,process_status=?,process_started=?',
              $p, null, true);
      print 'INSERTED OR UPDATED: ' . $r;
      print 'INSERTED OR UPDATED: ' . $sql;
      var_export($p);
    }
    $started = true;
    db::exec('UNLOCK TABLES');
    return $started;
  }
  
  function quit(){
    $this->do_quit = true;
  }
  
  function __construct(){
    $this->process_id = 'sic_socket';
  }
  
  function broadcast($data){
    if(!empty($data)){
      parent::broadcast( json_encode($data) );
    }
  }
  
}
