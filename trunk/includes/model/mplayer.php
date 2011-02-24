<?php

require_once APP_LIB . 'System/Daemon.php';
define('MPLAYER_CONTROL_FILE', '/home/www/music/out/player/control');
// cvlc --rc-host localhost:12344

class mplayer{
  static $running = false;
  static $data = array();
  static $settings = array();
  static $cport   = 12346;
  
  static function play(){
    return $output;
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
      $command = 'php ' . APP_SCRIPTS . 'mplayer_start.php';
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
      
      System_Daemon::info('START VLC');
      
      $command = 'cvlc --extraintf=rc --rc-host=localhost:' . self::$cport . ' -I dummy --rc-fake-tty >/var/www/vlc_out 2>&1 &';
      //  -vvv
      System_Daemon::info('VLC_B4: ' . $command);
      exec($command, $output); 
      System_Daemon::info('VLC_AFTER: %s', var_export($output,true));
      
      $te = time();
      $tt = $te - $ts;
      System_Daemon::info('VLC_MPLAYER FINISHED: %s', $tt);
      $runningOkay = true;
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