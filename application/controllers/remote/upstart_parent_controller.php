<?php

class upstart_parent_controller extends my_controller {

  var $children = array();
  var $descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w"),
  );
  var $pipes = array();
  var $last_sent_cron = 0;
  var $special_check_last_sent = 0;
  var $cron = null;

  public function index($arg = NULL) {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    $upstart_parent_hostname = gethostname();
    $upstart_is_master = kb::config('KB_MASTER_HOSTNAME', 'k') == strtolower($upstart_parent_hostname);
    print 'UPSTART PARENT (master?:' . (int) $upstart_is_master . ') RUNNING on:' . $upstart_parent_hostname . PHP_EOL;

    // KILL ANY PREVIOUS PROCESSeS
    $this->kill_all_my_children();

    // HOW MANY DONGLES ARE CONNECTED
    exec('rtl_433 -k 2>&1', $output);
    $dongle_count = (int) array_shift($output);
    print('DONGLE COUNT:' . $dongle_count) . PHP_EOL;

    $this->spawn_children($output);
    if ($upstart_is_master) {
      $this->spaw_cron_tasks();
    }

    $this->loop();
    print '!!!!!!!!! upstart_parent_controller DID QUIT' . PHP_EOL;
  }

  public function loop() {
    $check_process = 0;
    $do_quit = FALSE;
    while (!$do_quit) {
      $do_quit = TRUE;
      $process_running_count = 0;
      $i = 0;

      foreach ($this->children as $process) {
        $info = proc_get_status($process['resource_id']);
        if ((int) $info['running'] || $check_process < 100) {
          $process_running_count++;
          $do_quit = FALSE;
          $line = fgets($process['pipes'][1]);
          $trimmed_line = trim($line);
          if (!empty($trimmed_line)) {
            $this->log($line);
            while (!empty($trimmed_line)) {
              $line = fgets($process['pipes'][1]);
              $trimmed_line = trim($line);
              if (!empty($trimmed_line)) {
                $this->log($line);
              }
            }
          } else {
            $check_process++;
            usleep(14201);
          }
        } else {
          $this->log('NOT RUNNING(' . $process['dongle_id'] . ')');
          print_r($process);
        }
      }
      if (!is_null($this->cron)) {
        $line = fgets($this->cron['pipes'][1]);
        $trimmed_line = trim($line);
        if (!empty($trimmed_line)) {
          $this->log('{CRON}' . $line);
          while (!empty($trimmed_line)) {
            $line = fgets($process['pipes'][1]);
            $trimmed_line = trim($line);
            if (!empty($trimmed_line)) {
              $this->log('{CRON}' . $line);
            }
          }
        }
      }

      if ($check_process % 1500 == 0) {
        //print 'UpstartProcess(es) Still Running:' . $process_running_count . PHP_EOL;
        $check_process = 100;
      }
    }
  }

  public function spaw_cron_tasks() {
    $child_script = kb::config('KB_CRON_TASKS_SCRIPT_PATH');
    $this->cron = array();

    $this->cron['resource_id'] = proc_open($child_script, $this->descriptorspec, $this->cron['pipes']);
    print 'PROC_OPEN [CRON]: ' . $child_script . PHP_EOL;
    sleep(1);
    stream_set_blocking($this->cron['pipes'][1], 0);
  }

  public function spawn_children($output = null) {
    $child_script_path = kb::config('KB_RTL_433_PROCESSOR_SCRIPT_PATH');
    foreach ($output as $dongle) {
      $child = array();
      $dongle_info = preg_split('`:`', $dongle, -1, PREG_SPLIT_NO_EMPTY);
      $child['dongle_id'] = $dongle_info[0];

      $child_script = $child_script_path . $dongle_info[0] . ' 2>&1';
      $child['resource_id'] = proc_open($child_script, $this->descriptorspec, $child['pipes']);
      print 'PROC_OPEN: ' . $child_script . PHP_EOL;
      $this->children[] = $child;
      sleep(1);
    }

    print 'ATTEMPT TO SET BLOCKING...' . PHP_EOL;
    foreach ($this->children as $process) {
      if (!empty($process['resource_id'])) {
        print 'SET STREAM BLOCKING:' . $process['dongle_id'] . '::OF::' . count($this->children) . PHP_EOL;
        stream_set_blocking($process['pipes'][1], 0);
      }
    }
  }

  public function kill_all_my_children() {
    exec("pgrep rtl_433", $output);
    if (count($output)) {
      foreach ($output as $line) {
        $kill = 'kill -9 ' . (int) $line;
        print $kill . PHP_EOL;
        exec("$kill");
        sleep(2);
      }
    } else {
      print 'NO RTL_433 PROCESSES RUNNING' . PHP_EOL;
    }
    // CHECK IF NEED TO KILL CRON_TASKS
  }

}
