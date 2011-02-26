<?php

class app {

  static $dsns = array();
  static $autoload_paths = array();
  static $args = null;
  static $args_position = 0;

  public static function control() {

    $path = app::next();
    $path = empty($path) ? 'main' : $path;
    $cname = "{$path}_controller";
    $controller = new $cname;
    $controller->control();
  }

  public static function read($file = null, $vars = array()) {
    $content = '';

    if (is_file($file)) {
      if (!empty($vars)) {
        extract($vars);
      }
      ob_start();
      include $file;
      $content = ob_get_contents();
      ob_end_clean();
    }
    return $content;
  }

  public static function dsn($data_source) {
    $key = empty($data_source) ? 'default' : $data_source;
    $ds = self::$dsns[$key];

    return $ds;
  }

  public static function init() {
    spl_autoload_register('app::autoload');
    self::$dsns['default'] = array('host' => 'mysql:host=localhost;dbname=sic', 'username' => 'music', 'password' => 'music');

    define('APP_INCLUDES', dirname(__file__) . '/');
    define('APP_DIR', dirname(APP_INCLUDES . '../../') . '/');
    define('APP_MODEL', APP_INCLUDES . 'model/');
    define('APP_VIEW', APP_INCLUDES . 'view/');
    define('APP_CONTROL', APP_INCLUDES . 'control/');
    define('APP_LIB', APP_INCLUDES . 'lib/');
    define('APP_SCRIPTS', APP_INCLUDES . 'scripts/');

    define('APP_WEBROOT', getcwd() . '/');
    define('APP_CSS', APP_WEBROOT . 'css/');
    define('APP_JS', APP_WEBROOT . 'js/');
    define('APP_IMAGES', APP_WEBROOT . 'images/');

    define('APP_LAYOUTS', APP_DIR . 'layouts/');
    define('APP_LAYOUT', 'default');
    define('LAYOUT_CSS', APP_LAYOUTS . APP_LAYOUT . '/css/');
    define('LAYOUT_VIEW', APP_LAYOUTS . APP_LAYOUT . '/view/');

    $default_search_paths = array(APP_LIB,APP_MODEL, APP_CONTROL, APP_MODEL.'search/', APP_MODEL.'process/', APP_VIEW);
    self::add_path($default_search_paths);

    if (isset($_SERVER["SERVER_NAME"])) {
      
      define('APP_CRON', false);
      define('APP_HOME', preg_match('`/$`', dirname($_SERVER["PHP_SELF"])) ? dirname($_SERVER["PHP_SELF"]) : dirname($_SERVER["PHP_SELF"]) . "/");

    } else {
      define('APP_CRON', true);
      define('APP_HOME', 'http://music/');
    }
    
    define('APP_HOME_CSS', APP_HOME . 'css/');
    define('APP_HOME_JS', APP_HOME . 'js/');
    define('APP_HOME_IMAGES', APP_HOME . 'images/');
    define('APP_HOME_LAYOUT', APP_HOME . 'layouts/' . APP_LAYOUT . '/');
    

    self::parse_args();
    return true;
  }

  static function parse_args() {
    global $argv;
    self::$args = array();
    $dir    = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : APP_WEBROOT;
    $query  = isset($_SERVER['REQUEST_URI'])   ? $_SERVER['REQUEST_URI'] : $argv[1];
    //$query  = isset($argv) && !empty($argv) ? $argv[1] : $_SERVER['REQUEST_URI'];
    
    if(isset($argv)){
      $depth = 0;
    }else{
      $regex = '`' . $dir . '/*`';
      $subdir = preg_replace($regex, '', APP_WEBROOT);
      $subdir = preg_replace('`/$`', '', $subdir);
      $depth = empty($subdir) ? 0 : count(explode("/", $subdir));
      $pre = '/' . (empty($subdir) ? '' : $subdir . '/');
    }
    
    if (preg_match('`([^?#]*)`', trim($query), $matches)) {
      $t = trim($matches[1]);

      $ck = explode("/", $t);
      foreach ($ck as $k) {
        $k = trim($k);
        if ('' !== $k) {
          array_push(self::$args, $k);
        }
      }
      for ($i = 0; $i < $depth; $i++) {
        array_shift(self::$args);
      }
    }
  }

  public static function next() {
    $arg = '';
    if (self::$args_position < count(self::$args)) {
      $arg = self::$args[self::$args_position];
      self::$args_position++;
    }
    return $arg;
  }

  public static function autoload($class) {
    foreach (self::$autoload_paths as $path) {
      $check_path = $path . DIRECTORY_SEPARATOR . $class . '.php';
      if (is_readable($check_path)) {
        require_once($check_path);
        return true;
      }
    }
    return false;
  }

  public static function add_path($path) {
    //print 'DS:' . DIRECTORY_SEPARATOR;
    if (is_array($path)) {
      foreach ($path as $p) {
        self::$autoload_paths = array_unique(array_merge(self::$autoload_paths, array($p)));
      }
    } else {
      self::$autoload_paths = array_unique(array_merge(self::$autoload_paths, array($path)));
    }
  }

}
