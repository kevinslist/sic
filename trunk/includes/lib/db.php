<?php

class db {

  static $db_connections;
  static $con         = null;
  static $s           = null;
  static $force_new   = false;
  
  static function connect($data_source=null) {
  	$x = empty($data_source) ? 'default' : $data_source;
    if(!isset(db::$db_connections) || empty(db::$db_connections)){
      db::$db_connections = array();    
    }
    if (self::$force_new || !isset(db::$db_connections[$x])) {
      db::$db_connections[$x] = self::make_connection(app::dsn($data_source));
    } else {
      db::$con = $db_connections[$x];
    }
  }

  static function make_connection($dsn) {
    self::$con = new PDO($dsn['host'], $dsn['username'], $dsn['password']);
    return self::$con;
  }

  static function exec($sql, $p = null, $data_source=null) {
    self::connect($data_source);

    if (is_null($p)) {
      $count = self::$con->exec($sql);
    } else {
      self::$s = self::$con->prepare($sql);
      self::bind($p);
      $count = self::$s->execute();
    }
    return $count;
  }

  static function query($sql, $p = null, $data_source=null) {
    self::connect($data_source);
    $r = array();
    $error = false;
    
    if (is_null($p)) {
      self::$s = self::$con->query($sql);
    } else {
      self::$s = self::$con->prepare($sql);
      self::bind($p);
      self::$s->execute();
    }

    if (self::$s) {
      while ($row = self::$s->fetch(PDO::FETCH_ASSOC)) {
        $r[] = $row;
      }
      $t = self::$s->errorInfo();
      $error = !empty($t[2]);
      
    } else {
      $error = true;
    }
    if($error){
      self::log('ERROR:' . self::$s->errorInfo());
      //throw new Exception(var_export(self::$s->errorInfo(), true));
    }
    return $r;
  }

  static function row($sql, $p = null, $ds=null) {
    $r = self::query($sql, $p, $ds);
    return empty($r) ? array() : array_shift($r);
  }

  static function val($sql, $p = null, $ds=null) {
    $r = self::row($sql, $p, $ds);
    return empty($r) ? array() : array_shift($r);
  }

  static function vals($sql, $p = null, $ds=null) {
    $rs = self::query($sql, $p, $ds);
    $s  = array();
    foreach($rs as $r){
      $s[] = array_shift($r);
    }
    return $s;
  }

  static function last_id() {
    return self::$con->lastInsertId();
  }

  static function bind($p) {
    $p = is_array($p) ? $p : array($p);
    $i = 1;
    foreach ($p as $v) {
      if (is_null($v)) {
        self::$s->bindValue($i, null, PDO::PARAM_NULL);
      } elseif (is_int($v)) {
        self::$s->bindValue($i, $v, PDO::PARAM_INT);
      } elseif (is_float($v)) {
        self::$s->bindValue($i, $v, PDO::PARAM_INT);
      } elseif (is_string($v)) {
        self::$s->bindValue($i, $v, PDO::PARAM_STR);
      } elseif (is_bool($v)) {
        self::$s->bindValue($i, $v, PDO::PARAM_BOOL);
      } else {
        self::$s->bindValue($i, $v, PDO::PARAM_LOB);
      }
      $i++;
    }
  }
  
  public static function log($obj){
    
    if (is_resource(STDOUT)){ 
      print_r($obj);
      
    }else{
        $t = var_export($obj, true);
        System_Daemon::info('DB ERROR: %s', $t);
    }
  }

}