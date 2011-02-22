<?php

require_once APP_LIB . 'System/Daemon.php';
define('MPLAYER_CONTROL_FILE', '/home/www/music/out/player/control');

class mplayer{
  static $running = false;
  static $data = array();
  static $settings = array();
  
  static function pause(){
   // print "FPC: " . file_put_contents(MPLAYER_CONTROL_FILE, "pause\n");
    
    $fifo = fopen(MPLAYER_CONTROL_FILE, 'w+'); 
    var_export($fifo);
    fwrite($fifo, "pause\n");
    fclose ($fifo);
    
    return array('track_id'=>'pause');
  }
  static function seek(){
   // print "FPC: " . file_put_contents(MPLAYER_CONTROL_FILE, "pause\n");
    
    $fifo = fopen(MPLAYER_CONTROL_FILE, 'w+'); 
    System_Daemon::info('SEEK: %s', var_export($fifo,true));
    fwrite($fifo, "seek 2 50");
    return array('track_id'=>'pause');
  }
  
  static function start($tid){
    System_Daemon::setOptions(self::daemon_options());
    if(!System_Daemon::isRunning()){
      $command = 'php ' . APP_SCRIPTS . 'mplayer_start.php ' . $tid;
      shell_exec($command); 
    }
    return array('track_id'=>$tid);
    
  }
  
  static function start_player($track_id = 0){
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
      
      System_Daemon::info('TRACK ID: %s',$track_id);
      $r = db::row('SELECT * FROM tracks WHERE track_id = ?', (int)$track_id);
      System_Daemon::info('TRACK PATH: %s', $r['track_path']);
      
      $control = '/home/www/music/out/player/control';
      // $control = '/tmp/control';
      // $fh=fopen($control, "r+"); 
      // stream_set_blocking($fh, false);
      if(file_exists(MPLAYER_CONTROL_FILE)){
        unlink(MPLAYER_CONTROL_FILE);
      }
      umask(0);
      $mkfifo = posix_mkfifo(MPLAYER_CONTROL_FILE, 0600); 
      System_Daemon::info('sukMkFIFO: %s', $mkfifo);
      
      $command = 'mplayer -slave -input file=' . MPLAYER_CONTROL_FILE . ' "' . $r['track_path'] . '" > /tmp/kbmplayerout 2>&1 &'; 
      // -v -quiet 
      //// 2>&1 redirect errors to stdout
      // & don't wait
      //-really-quiet -slave 
      System_Daemon::info('COMMAND: %s', $command);
      
      exec($command, $output); 
      
      $te = time();
      $tt = $te - $ts;
      System_Daemon::info('COMMAND FINISHED: %s', $tt);
      System_Daemon::info('OP: %s', var_export($output, true) );
      $runningOkay = true;
      self::seek();
      
      while (!System_Daemon::isDying() && $runningOkay) {
        // What mode are we in?
        // play next ?
        $runningOkay = false; // check supin
        System_Daemon::iterate(1);
      }

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
    return array(
        'appName' => 'sic_mplayer',
        'appDir' => dirname(__FILE__),
        'appDescription' => 'sic_mplayer',
        'authorName' => 'brewsyourdaddy',
        'authorEmail' => 'brewsyourdaddy@gmail.com',
        'sysMaxExecutionTime' => '0',
        'sysMaxInputTime' => '0',
        'sysMemoryLimit' => '1024M',
        'appRunAsGID' => $run_id,
        'appRunAsUID' => $run_id,
        'usePEAR' => false,
        'logLocation' => APP_DIR . 'out/log/sic_mplayer/sic_mplayer.log',
        'appPidLocation' => APP_DIR . 'out/pid/sic_mplayer/sic_mplayer.pid',
        'logVerbosity' => 0
    );
  }

}