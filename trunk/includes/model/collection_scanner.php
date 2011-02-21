<?php

require_once APP_LIB . 'System/Daemon.php';
define('COLLECTION_SCANNER_STATUS_COMPLETE', 0);
define('COLLECTION_SCANNER_STATUS_INIT', 5);
define('COLLECTION_SCANNER_STATUS_INIT_SCANNED', 10);
define('COLLECTION_SCANNER_STATUS_PARSE_FILES', 15);
define('COLLECTION_SCANNER_STATUS_CLEANUP', 50);


class collection_scanner {
  static $running = false;
  static $tree = array();
  static $data = array();
  static $settings = array();
  static $song_filter  = array('mp3', 'm4a', 'flac', 'm4b', 'wma', 'mp4', 'mpg', 'm4r', 'mid', 'mpg', 'm4p');
  static $image_filter = array('jpg', 'jpeg', 'gif', 'png', 'xcf', 'bmp');
  
  static function settings(){
    self::$settings = db::settings('SELECT * FROM settings_collection_scanner');
    if(!self::$running){
      System_Daemon::setOptions(self::daemon_options());
    }
    self::$settings['directories'] = settings::vals('collection_scanner_directory');
    self::$settings['running'] = System_Daemon::isRunning();
    return self::$settings;
  }
  
  static function save_setting($name, $value){
    self::$settings[$name] = $value;
    return db::exec('INSERT INTO settings_collection_scanner (name, value) VALUES(?,?) ON DUPLICATE KEY UPDATE value=?', array($name, $value, $value));
  }

  static function menu() {
    return self::settings();
  }

  static function start_collection_scanner() {
    System_Daemon::setOptions(self::daemon_options());
    if(!System_Daemon::isRunning()){
      $command = 'php ' . APP_SCRIPTS . 'collection_scanner_start.php';
      shell_exec($command); 
    }
    return self::settings();
  }

  static function start_collection_scanner_daemon() {
    $options = self::daemon_options();
    $options['logVerbosity'] = 6;
    System_Daemon::setOptions($options);
    
    if(!System_Daemon::isRunning()){
      db::$force_new = true;
      System_Daemon::start();
      self::$running = true;
      self::close_output();
      self::$settings = self::settings();
      
      if(!(int)self::$settings['status']){
        self::save_setting('id', time());
        self::save_setting('status', COLLECTION_SCANNER_STATUS_INIT);
      }
      
      System_Daemon::info('memory_limit: %s', ini_get('memory_limit'));
      while(COLLECTION_SCANNER_STATUS_COMPLETE != (int)self::$settings['status'] && !System_Daemon::isDying()){
        System_Daemon::info('STEP_START: %s', self::$settings['status']);
        self::save_setting('status', self::step_collection_scanner());        
        System_Daemon::info('STEP_END: %s', self::$settings['status']);
      }
      
      $te = time();
      $tt = $te - (int)self::$settings['id'];
      System_Daemon::info('TIME TOTAL: %s', $tt);
      System_Daemon::info('memory_get_usage: %s', memory_get_usage(true));
      self::save_setting('status', COLLECTION_SCANNER_STATUS_COMPLETE);
      System_Daemon::stop();
    }
  }
  
  static function step_collection_scanner(){
    $new_status = COLLECTION_SCANNER_STATUS_COMPLETE;
    
    switch((int)self::$settings['status']){
      
      case(COLLECTION_SCANNER_STATUS_INIT):
        self::begin_collection_scanning();
        $new_status = COLLECTION_SCANNER_STATUS_INIT_SCANNED;
        break;
      case(COLLECTION_SCANNER_STATUS_INIT_SCANNED):
        self::scan_modified_directories();
        $new_status = COLLECTION_SCANNER_STATUS_PARSE_FILES;
        break;
      case(COLLECTION_SCANNER_STATUS_PARSE_FILES):
        self::parse_modified_files();
        $new_status = COLLECTION_SCANNER_STATUS_CLEANUP;
        break;
      case(COLLECTION_SCANNER_STATUS_CLEANUP):
        // do something
        //$new_status = 0;
        break;
      
      
      default:
        break;
    }
    return $new_status;
  }
  
  
  function parse_modified_files(){
    $tracks = db::query('SELECT track_id,track_path FROM tracks WHERE last_mod > prev_scan');
    if(count($tracks) > 0){
      require_once APP_MODEL . 'track_saver.php';
      $i = 0;
      foreach($tracks as $t){
        track_saver::update_from_file($t['track_id'], $t['track_path']);
        db::exec('UPDATE tracks SET prev_scan = ? WHERE track_id = ?', array((int)self::$settings['id'], (int)$t['track_id']));
      }
    }
    
  }
  
  
  
  
  
  static function scan_modified_directories(){
    $dirs = db::vals('SELECT name FROM dirs WHERE last_mod > prev_scan');      
    System_Daemon::info('scan_modified_directories: %s', count($dirs));
    
    foreach($dirs as $d){
      self::update_tracks(self::scan_directory($d));  
      db::exec('UPDATE dirs SET prev_scan = ? WHERE name = ?', array((int)self::$settings['id'], $d));
    }
  }
  
  
  
  static function update_tracks($directory){     
    //System_Daemon::info('update_tracks: %s', count($info['songs']));
    
    /* take images from directory and insert, then associate song with image */
    
    foreach($directory['songs'] as $track_path => $last_mod){
        db::exec('INSERT INTO tracks (track_path, last_mod, scan_id, track_added) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE last_mod=?, scan_id=?',
          array($track_path, (int)$last_mod, (int)self::$settings['id'], (int)self::$settings['id'], (int)$last_mod, (int)self::$settings['id']) );
    }
    
  }
  
  static function scan_directory($directory = false){

    $info = array('songs'=>array(), 'images'=>array());
    
    if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory)) {
      db::exec('DELETE FROM dirs WHERE name=?', $directory);
    } else {
      $directory_list = opendir($directory);
      while ($file = readdir($directory_list)) {
        if ($file != '.' && $file != '..') {
          $path = $directory . '/' . $file;
          if (is_readable($path) && is_file($path)) {
              $extension = strtolower(end(explode('.', $file)));
              
              if ('' == $extension || in_array($extension, self::$song_filter)) {
                $info['songs'][$path] = filemtime($path);
              }elseif(in_array($extension, self::$image_filter)) {   
                $info['images'][$path] = filemtime($path);
              }else{    
                db::exec('INSERT INTO xdev_other_files (path) values(?)', $path);
              }
          }
        }
      }
      closedir($directory_list);
      //time()
    }
    return $info;
  }
  
  
  static function begin_collection_scanning(){
    foreach(self::$settings['directories'] as $d){   
      System_Daemon::info('SCAN REC DIR 4 Changes: %s', $d);
      self::scan_directory_recursively($d);
    }
    db::exec('DELETE FROM dirs WHERE scan_id <> ?', (int)self::$settings['id']);
  }
  

  function scan_directory_recursively($directory) {
    $count = -1;
    $mp3_count = 0;
    $max_mod = 0;
    
    $dir = false;
    if (substr($directory, -1) == '/') {
      $directory = substr($directory, 0, -1);
    }
    if (!file_exists($directory) || !is_dir($directory)) {
      return FALSE;
    } elseif (is_readable($directory)) {
      $count++;
      $directory_list = opendir($directory);
      
      while ($file = readdir($directory_list)) {
        if ($file != '.' && $file != '..') {
          $count++;
          $path = $directory . '/' . $file;
          if (is_readable($path)) {
            if (is_dir($path)) {
              self::scan_directory_recursively($path);
            }elseif(is_file($path)){
              $extension = strtolower(end(explode('.', $file)));
              if (empty($extension) || in_array($extension, self::$song_filter)) {
                $mp3_count++;
                $mf = filemtime($path);
                $max_mod = $mf > $max_mod ? $mf : $max_mod;
              }
              /*
               * else if(!in_array($extension, self::$image_filter)){
               
                //db::exec('INSERT INTO xdev_extensions (name) VALUES (?)', $extension);
              }
               * 
               */
            }
          }
        }
      }
      
      closedir($directory_list);
      
      if($mp3_count > 0){
        //$m = filemtime($directory);
        $m = $max_mod;
        db::exec('INSERT INTO dirs (name,last_mod,scan_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE last_mod=?, scan_id=?',
          array($directory, (int)$m, (int)self::$settings['id'], (int)$m, (int)self::$settings['id']) );
  
      }else if(0==$count){
        // delete $directory
        // System_Daemon::info('DELETE DIR: %s', $directory);
      }
      
    }
    return;
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
        'appName' => 'sic_collection_scanner',
        'appDir' => dirname(__FILE__),
        'appDescription' => 'collection_scanner',
        'authorName' => 'brewsyourdaddy',
        'authorEmail' => 'brewsyourdaddy@gmail.com',
        'sysMaxExecutionTime' => '0',
        'sysMaxInputTime' => '0',
        'sysMemoryLimit' => '1024M',
        'appRunAsGID' => $run_id,
        'appRunAsUID' => $run_id,
        'usePEAR' => false,
        'logLocation' => APP_DIR . 'out/log/sic_collection_scanner/sic_collection_scanner.log',
        'appPidLocation' => APP_DIR . 'out/pid/sic_collection_scanner/sic_collection_scanner.pid',
        'logVerbosity' => 0
    );
  }

}
