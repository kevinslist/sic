<?php

class library_controller extends my_controller {

  static $song_filter = array('mp3', 'm4a', 'flac', 'm4b', 'wma', 'mp4', 'mpg', 'm4r', 'mid', 'mpg', 'm4p');
  static $image_filter = array('jpg', 'jpeg', 'gif', 'png', 'xcf', 'bmp');
  var $settings = NULL;

  public function __construct() {
    parent::__construct();
  }

  public function info() {
    phpinfo();
    die();
  }

  public function import() {
    $script = dirname(dirname(dirname(__FILE__))) . '/index.php';
    $full_script = "nice /opt/local/bin/php {$script} library import_folder > /opt/local/apache2/logs/sic.log 2>&1 & echo $!";
    $output = shell_exec($full_script);
    print '1';
  }

  function parse_folders() {
    $this->log_import('parse_folders: ' . (int)$this->settings['library_import_status']);
    if(1 == (int)$this->settings['library_import_status']){
        $dir = 'start-loop';
        while (!empty($dir)) {
          kb::db_exec('LOCK TABLE dirs WRITE');
          $dir = kb::db_value('SELECT name FROM dirs LIMIT 0, 1');
          if (!empty($dir)) {
            kb::db_delete('dirs', array('name' => $dir));
          }
          kb::db_exec('UNLOCK TABLES');
          $this->log_import('UNLOCK TABLES: ' . $dir);
          $info = $this->scan_directory($dir);
          $dir = NULL;
          $this->log_import('info: ' . var_export($info, TRUE));
        }
    }
  }
  
  static function scan_directory($directory = false){

    $info = array('songs'=>array(), 'images'=>array());
    
    if (file_exists($directory) && is_dir($directory)  && is_readable($directory)) {
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

  public function import_folder() {
    $this->settings = settings::get();
    $library_import_status = (!isset($this->settings['library_import_status']) || empty($this->settings['library_import_status'])) ? 0 : (int) $this->settings['library_import_status'];
  

    $this->log_import('import_folder:' . $library_import_status);
    if (empty($library_import_status)) {
      $this->settings['library_import_status'] = 1;
      $this->settings['library_import_id'] = time();
      settings::save($this->settings);
      $this->traverse_folders($this->settings['current_import_path']);
    }
    $this->log_import('call parse_folders()');
    $this->parse_folders();
  }

  public function traverse_folders($directory = NULL) {
    if (!empty($directory)) {
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
                $this->traverse_folders($path);
              } elseif (is_file($path)) {
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

        if ($mp3_count > 0) {
          //$m = filemtime($directory);
          $m = $max_mod;
          kb::db_exec('INSERT INTO dirs (name,last_mod,scan_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE last_mod=?, scan_id=?', array($directory, (int) $m, (int) $this->settings['library_import_id'], (int) $m, (int) $this->settings['library_import_id']));
        } else if (0 == $count) {
          // delete $directory
          // System_Daemon::info('DELETE DIR: %s', $directory);
        }
      }
    }
    return;
  }

  /*
    print (int)$settings['library_import_status'];
    $new_status = COLLECTION_SCANNER_STATUS_COMPLETE;

    switch((int)self::$settings['library_import_status']){

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

   */

  public function log_import($m = NULL, $d = NULL) {
    $p = array('log_time' => time(), 'log_message' => $m, 'log_data' => $d);
    kb::db_insert('log_library_scan', $p);
  }

}

/*
 * 

    //$full_script = "nohup /usr/bin/php '{$script}' > /dev/null & echo $!";
    //$full_script = "php {$script} library import_folder"; // > /dev/null & echo $!
    //$full_script = "/usr/bin/nohup php {$script} library import_folder >> /dev/null";
    //$full_script = "/usr/bin/nohup /opt/local/bin/php {$script} library import_folder 2>&1 & echo $!";
    //$full_script = "nohup php {$script} library import_folder 2>&1 & echo $!";
    //$full_script = "nohup /opt/local/bin/php 2>&1";
 */