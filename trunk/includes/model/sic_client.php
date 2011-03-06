<?php

class sic_client {
  static $username = false;
  
  static function name(){
    if(!self::$username){
      if(isset($_COOKIE["sic_username"])){
        // validate username?
        //self::$username = db::val('SELECT sic_username FROM users WHERE sic_username=?', self::safe_chars($_COOKIE["sic_username"]));
        self::$username = $_COOKIE["sic_username"];
      }
    }
    return self::$username;
  }
  
  static function login(){
    if(isset($_POST['sic_username']) && !empty($_POST['sic_username'])){
      self::save_login($_POST['sic_username']);
    }
    self::redirect();
  }
  
  static function save_login($n){
    $r = 0;
    $sic_username = self::safe_chars($n);
    if(!empty($sic_username)){
    $t = time();
    $r = db::exec('INSERT INTO users (sic_username, last_login) VALUES(?,?) ON DUPLICATE KEY UPDATE last_login=?',
            array($sic_username, $t, $t));
    }
    if($r){
      setcookie('sic_username', $sic_username, time()+60*60*24*7*365);
    }
    return $r;
  }
  
  static function safe_chars($in){
    $out = preg_replace('/[^a-z]*/i','', $in);
    $out = strtolower($out);
    return $out;
  }
  
  static function redirect($url=''){
    if(empty($url)){
      $url = APP_HOME;
    }
    header("Location: " . $url);
    die();
  }
}

