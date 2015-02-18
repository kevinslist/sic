<?php

/*
 * itach api id: 057d6b19-5f2c-4deb-bd7c-31f659caaf4e
 * for web interface: https://irdatabase.globalcache.com
 * https://irdatabase.globalcache.com/api/v1/057d6b19-5f2c-4deb-bd7c-31f659caaf4e/manufacturers
 */

class queue_controller extends my_controller {

  public function __construct() {
    parent::__construct();
  }

  public function index($base_64_command = NULL) {
    $current_signal = unserialize(base64_decode(urldecode($base_64_command)));
    if (is_array($current_signal) && !empty($this->current_signal)) {
      config_router::queue($current_signal);
    }
  }

}
