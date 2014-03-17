<?php

if (!defined('BASEPATH')){  exit('No direct script access allowed'); }

class my_controller extends kb_controller {

  public function __construct() {
    parent::__construct('sic-bootstrap-1');
    $this->config->load('assets');
    $this->load->database();
    $this->load->helper('url');
    $this->load->model('client');
    spl_autoload_register('my_controller::autoload');
  }

  public static function autoload($class) {
    $found = false;
    $paths = array(
        'core' => strtolower(dirname(__FILE__) . DIRECTORY_SEPARATOR . $class . '.php'),
    );
    foreach ($paths as $k => $path) {
      if (is_readable($path)) {
        require_once($path);
        $found = true;
        break;
      }
    }

    return $found;
  }

}