<?php

class queue_controller extends my_controller {

  public function index($base_64_command = NULL) {

    $signal = unserialize(base64_decode(urldecode($base_64_command)));

    if (is_array($signal) && !empty($signal['remote_command_remote_id']) 
                     && !empty($signal['remote_command_signal_id']) ) {
      if(isset(config_remote::$remote_map[$signal['remote_command_remote_id']])){
        $signal['remote_command_inserted_time'] = time();
        kb::db_insert('remote_commands', $signal);
      }
    }else {
      //print '<<< ! QUEUE SIGNAL RECEIVED INVALID >>>' . PHP_EOL;
    }
  }
}
