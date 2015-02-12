<?php

class rtl_433_controller extends my_controller {

  var $descriptorspec = array(
      0 => array("pipe", "r"),
      1 => array("pipe", "w"),
      2 => array("pipe", "w"),
  );
  
  var $pipes;
  var $do_quit = false;
  var $empty_frames = 0;
  var $signal_started = false;
  var $signal;
  var $last_pulse;
  var $dongle_index = 0;
  var $dongle_inited = false;

  public function start($arg = NULL) {
    print 'DONGLE(' . $arg . ') RUNNING...' . PHP_EOL;
    $command = 'rtl_433 -d ' . $arg . '  2>&1';
    $this->dongle_index = $arg;
    $process = proc_open($command, $this->descriptorspec, $this->pipes);
    while (!$this->do_quit) {
      $line = fgets($this->pipes[1]);
      $this->process_line($line);
    }
  }

  public function process_line($l) {
    if ($this->dongle_inited) {
      $frames = (int) $l;
      if ($this->signal_started) {
        if ($frames < 0) {
          $this->empty_frames += $frames;
        } else {
          $this->empty_frames = 0;
          $this->last_pulse = $l;
        }
        $signal[] = $frames;
      } else {
        $this->signal_started = true;
        $this->empty_frames = 0;
        $this->last_pulse = $l;
        $signal = array(
            'pulses' => [$frames]
        );
      }
      if ($this->signal_started && $this->empty_frames < 20000) {
        $this->signal_started = false;
        $this->process_signal($signal);
      }
    } else {
      $this->log($l);
      if (preg_match('`kb433_start`i', $l)) {
        $this->dongle_inited = true;
      }
    }
  }

  public function process_signal($signal) {
    print PHP_EOL . 'PROCESS SIGNAL:' . PHP_EOL;
    print_r($signal);
    print PHP_EOL;
  }
  
  public function log($str){
    print 'LOG(' . $this->dongle_index . '):' . $str . PHP_EOL;
  }

}
