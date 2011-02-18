<?php

require_once APP_LIB . 'System/Daemon.php';
define('COLLECTION_SCANNER_STATUS_COMPLETE', 0);
define('COLLECTION_SCANNER_STATUS_INIT', 1);
define('COLLECTION_SCANNER_STATUS_INIT_SCANNED', 2);

define('COLLECTION_SCANNER_STATUS_CLEANUP', 5);


class collection_scanner {
  static $running = false;
  static $tree = array();
  static $data = array();
  static $settings = array();
  static $song_filter  = array('mp3', 'm4a', 'flac', 'm4b', 'wma', 'mp4', 'mpg', 'm4r', 'mid', 'mpg', 'm4p');
  static $image_filter = array('jpg', 'jpeg', 'gif', 'png', 'xcf', 'bmp');
  
  static function settings(){
    self::$settings = db::settings('SELECT * FROM collection_scanner_settings');
    if(!self::$running){
      System_Daemon::setOptions(self::daemon_options());
    }
    self::$settings['directories'] = settings::vals('collection_scanner_directory');
    self::$settings['running'] = System_Daemon::isRunning();
    return self::$settings;
  }
  
  static function save_setting($name, $value){
    self::$settings[$name] = $value;
    return db::exec('INSERT INTO collection_scanner_settings (name, value) VALUES(?,?) ON DUPLICATE KEY UPDATE value=?', array($name, $value, $value));
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
  
  static function scan_modified_directories(){
    require_once APP_LIB . 'getid3/getid3/getid3.php';
    $dirs = db::vals('SELECT name FROM dirs WHERE lastmod > prev_scan');      
    System_Daemon::info('scan_modified_directories: %s', count($dirs));
    $i = 0;
    
    $getID3 = new getID3;
    
    foreach($dirs as $d){
      $info = self::scan_directory($d);  
      
      $empty = count($info['songs']);
      
      foreach($info['songs'] as $s){
        
        System_Daemon::info('PARSE: %s', $s);
        $data = $getID3->analyze($s);
        //getid3_lib::CopyTagsToComments($data);
        
        foreach($data as $k=>$v){
          System_Daemon::info('KEY: %s', $k);
          if('comments_html' == $k){
            foreach($v as $c=>$f){
              System_Daemon::info('COMMENT(%s): %s', $c, $f);
            }
            
          }elseif('id3v2' == $k){
            foreach($v as $c=>$f){
              System_Daemon::info('id3v2 [%s]: %s', $c, $f);
            }
         
          }else{
            System_Daemon::info('VALUE: %s', $v);
          }
        }
        
      }
      
      db::exec('UPDATE dirs SET prev_scan = ? WHERE name = ?', array($empty, $directory));
      
      if($i++ > 3){
        break;
      }
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
                $info['songs'][] = $path;
              }elseif(in_array($extension, self::$image_filter)) {   
                $info['images'][] = $path;
              }else{    
                db::exec('INSERT INTO other_files (path) values(?)', $path);
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
      self::scan_directory_recursively($d);
    }
    db::exec('DELETE FROM dirs WHERE scan_id <> ?', (int)self::$settings['id']);
  }
  

  function scan_directory_recursively($directory) {
    $count = -1;
    $mp3_count = 0;
    
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
              
              if ('' == $extension || in_array($extension, self::$song_filter)) {
                $mp3_count++;
              }else if(!in_array($extension, self::$image_filter)){
                db::exec('INSERT INTO extensions (name) VALUES (?)', $extension);
              }
            }
          }
        }
      }
      
      closedir($directory_list);
      
      if($count > 0 && $mp3_count > 0){
        $m = filemtime($directory);
        db::exec('INSERT INTO dirs (name,lastmod,scan_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE lastmod=?, scan_id=?',
          array($directory, (int)$m, (int)self::$settings['id'], (int)$m, (int)self::$settings['id']) );
  
      }else if(0==$count){
        // delete $directory
        //System_Daemon::info('DELETE DIR: %s', $directory);
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
