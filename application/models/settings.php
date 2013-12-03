<?php

class settings{
  
  static function save($settings = NULL){
    if(!empty($settings)){
      foreach($settings as $setting_name => $setting_value){
        kb::db_exec('INSERT IGNORE INTO settings (setting_name, setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=?', array($setting_name, $setting_value, $setting_value));
      }
    }
  }
  static function get(){
    $return_settings = array();
    $settings = kb::db_get('settings');
    foreach($settings as $s){
      $return_settings[$s['setting_name']] = $s['setting_value'];
    }
    return $return_settings;
  }
  
  
  
}