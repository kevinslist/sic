<?php

class upstart_parent_controller extends my_controller {

  var $children = array();
  var $descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w"),
  );
  var $pipes = array();

  public function index($arg = NULL) {
    $this->kill_all_my_children();

    $this->load->helper('process_kbrtl');
    $child_script_path = 'php ' . dirname(dirname(dirname(__FILE__))) . '/index.php rtl_433 start ';


    exec('rtl_433 -k 2>&1', $output);
    $dongle_count = (int) array_shift($output);
    print('DONGLE COUNT:' . $dongle_count) . PHP_EOL;
    foreach ($output as $dongle) {
      $dongle_info = preg_split('`:`', $dongle, -1, PREG_SPLIT_NO_EMPTY);
      $child_script = $child_script_path . $dongle_info[0] . ' 2>&1';
      print $child_script . PHP_EOL;
      $child = array();
      $child['resource_id'] = proc_open($child_script, $this->descriptorspec, $child['pipes']);
      $child['dongle_id'] = $dongle_info[0];
      $this->children[] = $child;
      sleep(3);
    }

    print 'ATTEMPT TO SET BLOCKING...' . PHP_EOL;
    foreach ($this->children as $process) {
      if (!empty($process['resource_id'])) {
        print 'SET STREAM BLOCKING:' . $process['dongle_id'] . '::OF::' . count($this->children) . PHP_EOL;
        stream_set_blocking($process['pipes'][1], 0);
      }
    }

    $check_process = 0;
    $do_quit = FALSE;
    error_reporting(E_ALL);
    ini_set('display_erros', 'On');
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
            print $line;
            while (!empty($trimmed_line)) {
              $line = fgets($process['pipes'][1]);
              $trimmed_line = trim($line);
              print $line;
            }
          } else {
            $check_process++;
            usleep(14201);
          }
        } else {
          print 'NOT RUNNING(' . $process['dongle_id'] . ')' . PHP_EOL;
          print_r($process);
        }
      }
      if ($check_process % 1500 == 0) {
        print 'UpstartProcess(es) Still Running:' . $process_running_count . PHP_EOL;
        $check_process = 100;
      }
    }
    print 'DID QUIT' . PHP_EOL;
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
  }

}
