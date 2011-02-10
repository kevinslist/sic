<?php

require_once APP_LIB . 'System/Daemon.php';

class collection_scanner {

  static $directory_tree = array();
  static $data;

  static function menu() {
    self::$data['lastrun'] = current(settings::val('collection_scanner_directory'));
    System_Daemon::setOptions(self::daemon_options());
    self::$data['collection_scanner_daemon_running'] = System_Daemon::isRunning();

    return self::$data;
  }

  static function start() {
    // if not running
    $command = 'php ' . APP_SCRIPTS . 'collection_scanner_start.php';
    shell_exec($command);
    System_Daemon::setOptions(self::daemon_options());
    self::$data['collection_scanner_daemon_running'] = System_Daemon::isRunning();
    return self::$data;
  }

  static function run_scanner() {
    $options = self::daemon_options();
    $options['logVerbosity'] = 6;
    System_Daemon::setOptions($options);
    System_Daemon::start();

    if (is_resource(STDOUT)){
      fclose(STDOUT);
    }
    if (is_resource(STDERR)){
      fclose(STDERR);
    }
    $ts = time();
    System_Daemon::info('TIME START: %s', $ts);
    
     //!System_Daemon::isDying()
    db::$force_new = true;
    $dirs = settings::val('collection_scanner_directory');
    foreach($dirs as $d){
      //System_Daemon::info('scanning: %s', $d);
      self::$directory_tree[$d] = self::scan_directory_recursively($d);
    }
    $te = time();
    $tt = $te - $ts;
    
    System_Daemon::info('TIME END: %s', $te);
    System_Daemon::info('TIME TOTAL: %s', $tt);
    System_Daemon::stop();
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
            //$subdirectories = explode('/', $path);
            if (is_dir($path)) {
              $directory_tree[] = array(
                  'path' => $path,
                  //'name' => end($subdirectories),
                  //'kind' => 'directory',
                  'content' => self::scan_directory_recursively($path, $filter));
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
      return $directory_tree;
    } else {
      return FALSE;
    }
  }

  static function daemon_options() {

    return array(
        'appName' => 'sic_collection_scanner',
        'appDir' => dirname(__FILE__),
        'appDescription' => 'collection_scanner',
        'authorName' => 'brewsyourdaddy',
        'authorEmail' => 'brewsyourdaddy@gmail.com',
        'sysMaxExecutionTime' => '0',
        'sysMaxInputTime' => '0',
        'sysMemoryLimit' => '1024M',
        'appRunAsGID' => 33,
        'appRunAsUID' => 33,
        'usePEAR' => false,
        'logLocation' => APP_DIR . 'out/log/sic_collection_scanner/sic_collection_scanner.log',
        'appPidLocation' => APP_DIR . 'out/pid/sic_collection_scanner/sic_collection_scanner.pid',
        'logVerbosity' => 0
    );
  }

}
