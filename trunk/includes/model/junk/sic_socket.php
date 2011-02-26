<?php

require_once APP_LIB . 'System/Daemon.php';
require_once APP_LIB . 'websocket.class.php';
define('SIC_SOCKET_SLUG', 'sic_socket');

class sic_socket extends WebSocket{
  static $running = false;
  static $data = array();
  static $settings = array();
  
  function update_status(){
    //player::get_status();
    //System_Daemon::info('update_status');
    $this->broadcast( array('action'=>'status') );
  }
  
  function quit(){
    $this->do_quit = true;
  }
  
  function process($user,$msg){
    parse_str($msg);
    if(isset($action)){
      System_Daemon::info('PROCESS: %s', $action . '::' . $data);
    
      switch($action){
        case('play'):
          mplayer::play();
          break;
        case('quit'):
          $this->quit();
          $json = 'quit';
          break;
        case('track_info'):
          //$track_id = (int)$m['text'];
          $json = db::row('select * from tracks where track_id=?', (int)$data);
          break;
        default:
          $json = 'default';
          break;
      }
      $this->broadcast($json);
    }else{
      //System_Daemon::info('EMPTYREQUEST');
    }
  }
  
  function broadcast($data){
    //System_Daemon::info('BROADCASt: ' . $msg);
    
    if(!empty($data)){
      foreach($this->users as $user){
        if($user->handshake && $user->socket){
          //System_Daemon::info('SEND BAK: ' . json_encode($data));
          $this->send($user->socket, json_encode($data) );
        }
      }
    }
    
  }
  
  
  
  
  static function settings(){
    self::$settings = array();
    if(!self::$running){
      System_Daemon::setOptions(self::daemon_options());
    }
    self::$settings['running'] = System_Daemon::isRunning();
    return self::$settings;
  }
  
  static function init(){
    System_Daemon::setOptions(self::daemon_options());
    if(!System_Daemon::isRunning()){
      $command = 'php ' . APP_SCRIPTS . 'sic_socket_start.php';
      shell_exec($command); 
    }
    return self::settings();    
  }
  
  static function create($track_id = 0){
    $options = self::daemon_options();
    $options['logVerbosity'] = 6;
    System_Daemon::setOptions($options);
    
    if(!System_Daemon::isRunning()){
      db::$force_new = true;
      System_Daemon::start();
      System_Daemon::info('memory_limit: %s', ini_get('memory_limit'));
      $ts = time();
      self::$running = true;
      self::close_output();
      
      System_Daemon::info('START SOCKET SERVER');
      $m = new sic_socket("localhost",12345);
      $te = time();
      $tt = $te - $ts;
      System_Daemon::info('TIME TOTAL: %s', $tt);
      System_Daemon::info('memory_get_usage: %s', memory_get_usage(true));
      System_Daemon::stop();
    }
  }
  
  static function menu(){
    return array();
  }
  
  function say($str){
    System_Daemon::info('SS_SAY:' . $str);
  }
  function log($str){
    if($this->debug){ System_Daemon::info('SS_LOG:' . $str); }
  }
  
  static function close_output(){
    if (is_resource(STDOUT)){
      fclose(STDOUT);
    }
    if (is_resource(STDERR)){
      fclose(STDERR);
    }
  }
  
  static function daemon_options() {
    $run_id = (int)settings::val('run_id');
    $pid = APP_DIR . 'out/pid/sic_socket/sic_socket.pid';
    $log = APP_DIR . 'out/log/sic_socket/sic_socket.log';
    return array(
        'appName' => SIC_SOCKET_SLUG,
        'appDir' => dirname(__FILE__),
        'appDescription' => SIC_SOCKET_SLUG,
        'authorName' => 'brewsyourdaddy',
        'authorEmail' => 'brewsyourdaddy@gmail.com',
        'sysMaxExecutionTime' => '0',
        'sysMaxInputTime' => '0',
        'sysMemoryLimit' => '1024M',
        'appRunAsGID' => $run_id,
        'appRunAsUID' => $run_id,
        'usePEAR' => false,
        'logLocation' => $log,
        'appPidLocation' => $pid,
        'logVerbosity' => 0
    );
  }

}