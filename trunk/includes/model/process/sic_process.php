<?php

class sic_process{
  var $process_id   = 'process';
  var $process_pid  = 0;
  var $log_file     = '/var/www/sic_log.log';
  
  
  static function quit_process($pid){
    exec("kill -9 {$pid}");
  }
  
  static function quit(){
    $r = db::vals('SELECT process_pid FROM process_status WHERE process_status > 0');
    foreach($r as $pid){
      self::quit_process((int)$pid);
      db::exec('UPDATE process_status SET process_pid = 0, process_status=0 WHERE process_pid=?', (int)$pid);
    }
  }
  
  function run(){
    print 'default running';
  }
  
  function start(){
    if(!$this->running()){
      $command = 'nohup php ' . APP_DIR . 'index.php /process/run/' . $this->process_id . ' > ' . $this->log_file . ' 2>&1 & echo $!';
      exec($command);
    }
    return array_merge( array('running'=>$this->running()), $this->status());
  }
  
  function status($lock = false){
    $sql = 'SELECT * FROM process_status WHERE process_id=?';
    if($lock){
      db::exec('LOCK TABLE process_status READ');
      $status = db::row($sql, $this->process_id);
      db::exec('UNLOCK TABLES');
    }else{
      $status = db::row($sql, $this->process_id);
    }
    return $status;
  }
  
  public function running(){
    $running = false;
    $pid = (int)db::val('SELECT process_pid FROM process_status WHERE process_id=?', $this->process_id);
    if($pid){
      exec("ps ax | grep $pid 2>&1", $output);
      while( list(,$row) = each($output) ){
        $row_array = explode(" ", $row);
        $check_pid = (int)$row_array[0];
        if($pid == $check_pid) {
          $running = true;
          break;
        }
      }
    }
    return $running;
  }
  
  public function log($log = '', $log_level=255){
    //print $log;
    db::exec('INSERT INTO process_logs (process_id,process_log,
                process_log_level,process_pid) VALUES (?,?,?,?)',
            array($this->process_id, $log, $log_level, $this->process_pid));
  }
  
}