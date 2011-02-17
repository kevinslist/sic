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
    $dirs = db::vals('SELECT name FROM dirs WHERE lastmod > prev_scan');      
    System_Daemon::info('scan_modified_directories: %s', count($dirs));
    foreach($dirs as $d){
      self::scan_directory($d);      
    }
    
  }
  
  static function scan_directory($directory = false){
    $filter = array('mp3');
    $ps = 0;
    
    if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory)) {
      db::exec('DELETE FROM dirs WHERE name=?', $directory);
    } else {
      $directory_list = opendir($directory);
      while ($file = readdir($directory_list)) {
        if ($file != '.' && $file != '..') {
          $path = $directory . '/' . $file;
          if (is_readable($path) && is_file($path)) {
              $extension = strtolower(end(explode('.', $file)));
              
              if (in_array($extension, $filter)) {
                $ps++;
                // parse mp3 id3 / insert into DB or update
              }else{    
                db::exec('INSERT INTO other_files (path) values(?)', $path);
                
              }
          }
        }
      }
      closedir($directory_list);
      //time()
      db::exec('UPDATE dirs SET prev_scan = ? WHERE name = ?', array($ps, $directory));
    }
    return;
  }
  
  
  static function begin_collection_scanning(){
    foreach(self::$settings['directories'] as $d){
      self::scan_directory_recursively($d);
    }
    foreach(self::$tree as $d => $m){
      db::exec('INSERT INTO dirs (name,lastmod,scan_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE lastmod=?, scan_id=?',
              array($d, (int)$m, (int)self::$settings['id'], (int)$m, (int)self::$settings['id']) );
    }
    db::exec('DELETE FROM dirs WHERE scan_id <> ?', (int)self::$settings['id']);
  }
  

  function scan_directory_recursively($directory, $filter=FALSE) {
    //System_Daemon::info('in scan dir: %s', $directory);
    if (substr($directory, -1) == '/') {
      $directory = substr($directory, 0, -1);
    }
    if (!file_exists($directory) || !is_dir($directory)) {
      return FALSE;
    } elseif (is_readable($directory)) {
      $directory_list = opendir($directory);
      while ($file = readdir($directory_list)) {
        if ($file != '.' && $file != '..') {
          $path = $directory . '/' . $file;
          
          if (is_readable($path)) {
            if (is_dir($path)) {
              self::$tree[$path] = filemtime($path);
              self::scan_directory_recursively($path, $filter);
            } 
            /*elseif (is_file($path)) {
              $extension = end(explode('.', end($subdirectories)));
              if ($filter === FALSE || $filter == $extension) {
                $directory_tree[] = array(
                    'path' => $path,
                    'name' => end($subdirectories),
                    'extension' => $extension,
                    'size' => filesize($path),
                    'kind' => 'file');
              }
            }
             * 
             */
          }
        }
      }
      closedir($directory_list);
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
