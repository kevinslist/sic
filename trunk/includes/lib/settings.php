<?php
define('SETTINGS_DEFAULT_KEY', 'oynsadfkltb');
class settings{
  //static $default_key = 'oynhasltb';
	
  public static function val($key = null, $value = SETTINGS_DEFAULT_KEY){
    if(is_null($value)){
      return self::delete($key);
    }elseif(SETTINGS_DEFAULT_KEY == $value){
      return self::get($key);
    }else{
      return self::set($key, $value);
    }
  }
  
  public static function insert_value($key, $value){
    return db_exec('INSERT INTO settings (setting_name, setting_value) VALUES(?, ?)', array($key_value));
  }
	
  public static function set($key, $value = null ){
    self::delete($key);
    if(is_array($value)){
      foreach($value as $v){
        insert_value($key, $v);
      }
    }else{
      insert_value($key, $value);
    }
    return true;
  }
	
  public static function delete($key){
    return db::exec('DELETE FROM settings WHERE setting_name = ?', $key);
  }
  
  public static function get($key){
    return db::vals('SELECT setting_value FROM settings WHERE setting_name = ? ORDER BY setting_order', $key);
  }
  
  
  
}