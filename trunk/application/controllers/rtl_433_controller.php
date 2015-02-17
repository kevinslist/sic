<?php

class rtl_433_controller extends my_controller {

  var $descriptorspec = array(
    0 => array("pipe", "r"),
    1 => array("pipe", "w"),
    2 => array("pipe", "w"),
  );
  var $do_quit = false;
  var $empty_frames = 0;
  var $signal_started = false;
  var $last_pulse;
  var $dongle_index = 0;
  var $dongle_inited = false;
  var $signal = null;
  var $header_string = null;
  var $remote_string = null;
  var $is_repeat = 0;
  var $is_signal = false;

  public function start($arg = NULL) {
    ini_set('MAX_EXECUTION_TIME', -1);
    print 'DONGLE(' . $arg . ') RUNNING on:' . gethostname() . PHP_EOL;
    $this->dongle_index = $arg;
    //$command = 'rtl_433 -f ' . (433882002 + $this->dongle_index) . ' -s 425001 -d ' . $arg . '  2>&1';
    //$command = 'rtl_433 -f ' . (433869420 + 10*$this->dongle_index)  . $arg . '  2>&1';
    //$command = 'rtl_433 -f ' . (433882002 + $this->dongle_index) . ' -s 425001 -d ' . $arg . '  2>&1';
    //$command = 'rtl_433 -d ' . $arg . '  2>&1';
    $command = 'rtl_433 -f 433882002 -d ' . $arg . '  2>&1';
    $this->log('COMMAND:' . $command);
    $process = proc_open($command, $this->descriptorspec, $pipes);
    while (!$this->do_quit) {
      $line = fgets($pipes[1]);
      $this->process_line($line);
    }
    $this->log('START DID QUIT');
  }

  public function process_line($line_in) {
    $l = trim($line_in);

    if ($this->dongle_inited) {
      $frames = (int) $l;

      if ($this->signal_started) {

        if ($frames < 0) {
          $this->empty_frames++;
        } else {
          $this->empty_frames = 0;
          $this->last_pulse = $l;
        }
        array_push($this->signal, $frames);
      } elseif ($frames > 0) {
        $this->signal_started = true;
        $this->empty_frames = 0;
        $this->last_pulse = $l;

        $this->signal = array();
        array_push($this->signal, $frames);
      } else {
        //$this->log('SPACE:' . $l);
      }



      if ($this->signal_started && $this->empty_frames > 2) {
        $this->signal_started = false;
        $this->process_signal();
        $current_time_pieces = explode(':', $this->last_pulse);
        if($this->is_signal){
          $signal = array(
            'header-string' => $this->header_string,
            'remote-string' => $this->remote_string,
            'is-repeat' => $this->is_repeat,
            'last-signal' => (int)end($current_time_pieces),
          );
          $signal_serialized = serialize($signal);
          $signal_base64 = urlencode(base64_encode($signal_serialized));
          
          $this->is_repeat = false;
          $this->is_signal = false;
          
          $t = file_get_contents('https://k/cron.php/signal/validate/' . $signal_base64);
          $this->log($t);
        }
      }
    } else {
      if (preg_match('`kb433_start`i', $l)) {
        $this->dongle_inited = true;
        $this->log('DONGLE  INITIED:' . $this->dongle_index);
      } elseif (!is_int($l)) {
        $this->log('*INFO*:' . $l);
      }
    }
  }

  public function process_signal() {
    $found_positive = false;
    while (!$found_positive) {
      $val = (int) array_pop($this->signal);
      if ($val > 0) {
        $found_positive = true;
        array_push($this->signal, $val);
      }
    }
    $signal_count = count($this->signal);
    if (in_array($signal_count, array(87, 55))) {
      //$this->log('VALID SIGNAL: ' . $signal_count);
      $this->is_signal = true;
      $this->set_remote_header_code();
      $this->set_remote_code();
    } elseif (3 == $signal_count) {
      $this->is_repeat = true;
      $this->is_signal = true;
      //$this->log('REPEAT SIGNAL: ');
      //$this->log($this->last_pulse);
    } else {
      $this->log('OTHER SIGNAL:' . $signal_count);
    }
  }
  
  
  public function set_remote_code() {
    //print_r($this->signal);
    $max_space = 0;
    $min_space = 999990;
    $remote_spaces = array();
    
    for ($i = 23; $i < count($this->signal); $i += 2) {
      $v = abs((int) $this->signal[$i]);
      if ($v < $min_space) {
        $min_space = $v;
      }
      if ($v > $max_space) {
        $max_space = $v;
      }
      //$this->log('SIG:MAX:' . $max_space . ':MIN:' . $min_space . ":CUR:" . $v);
      $remote_spaces[] = $v;
    }
    $avg_min_total = $min_space;
    $avg_min_count = 1;

    $avg_max_total = $max_space;
    $avg_max_count = 1;

    foreach ($remote_spaces as $s) {
      $min_diff = $s - $min_space;
      $max_diff = $max_space - $s;
      if ($min_diff < $max_diff) {
        $avg_min_total += $s;
        $avg_min_count++;
      } else {
        $avg_max_total += $s;
        $avg_max_count++;
      }
    }
    $avg_min = floor($avg_min_total / $avg_min_count);
    $avg_max = ceil($avg_max_total / $avg_max_count);
    //$this->log('$avg_min:' . $avg_min . ':$avg_max:' . $avg_max);

    $remote_string = '';

    foreach ($remote_spaces as $s) {
      $min_diff = $s - $avg_min;
      $max_diff = $avg_max - $s;
      
      if($min_diff <= 50 && $max_diff <= 50){
        $remote_string .= '1';
      }elseif ($min_diff < $max_diff) {
        $remote_string .= '0';
      } else {
        $remote_string .= '1';
      }
    }
    $this->remote_string = $remote_string;
    //$this->log('$remote_string:' . $remote_string);
  }
  
  
  

  public function set_remote_header_code() {
    $max_space = 0;
    $min_space = 999990;
    $header_spaces = array();
    for ($i = 1; $i < 21; $i += 2) {
      $v = abs((int) $this->signal[$i]);
      if ($v < $min_space) {
        $min_space = $v;
      }
      if ($v > $max_space) {
        $max_space = $v;
      }
      //$this->log('HF:MAX:' . $max_space . ':MIN:' . $min_space . ":CUR:" . $v);
      $header_spaces[] = $v;
    }
    $avg_min_total = $min_space;
    $avg_min_count = 1;

    $avg_max_total = $max_space;
    $avg_max_count = 1;

    foreach ($header_spaces as $s) {
      $min_diff = $s - $min_space;
      $max_diff = $max_space - $s;
      if ($min_diff < $max_diff) {
        $avg_min_total += $s;
        $avg_min_count++;
      } else {
        $avg_max_total += $s;
        $avg_max_count++;
      }
    }
    $avg_min = floor($avg_min_total / $avg_min_count);
    $avg_max = ceil($avg_max_total / $avg_max_count);
    //$this->log('$avg_min:' . $avg_min . ':$avg_max:' . $avg_max);

    $header_string = '';

    foreach ($header_spaces as $s) {
      $min_diff = $s - $avg_min;
      $max_diff = $avg_max - $s;
      if ($min_diff < $max_diff) {
        $header_string .= '0';
      } else {
        $header_string .= '1';
      }
    }
    $this->header_string = $header_string;
    //$this->log('$header_string:' . $header_string);
  }

  public function log($str) {
    parent::log('LOG(' . $this->dongle_index . '):' . $str);
  }

}
