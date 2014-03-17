<?php

class settings {

  static function save($settings = NULL) {
    if (!empty($settings)) {
      foreach ($settings as $setting_name => $setting_value) {
        kb::db_exec('INSERT IGNORE INTO settings (setting_name, setting_value) VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=?', array($setting_name, $setting_value, $setting_value));
      }
    }
  }

  static function get($key = NULL, $unlock = TRUE) {
    kb::db_exec('LOCK TABLE settings WRITE');
    $val = kb::db_value('SELECT setting_value FROM settings WHERE setting_name=?', $key);
    if ($unlock) {
      kb::db_exec('UNLOCK TABLES');
    }
    return $val;
  }
  
  static function set($key = NULL, $value = NULL) {
    return kb::db_exec("INSERT IGNORE INTO settings (setting_name, setting_Value) VALUES(?,?) 
                        ON DUPLICATE KEY UPDATE setting_value=?", array($key, $value, $value));
  }

  static function get_values() {
    $return_settings = array();
    $settings = kb::db_get('settings');
    foreach ($settings as $s) {
      $return_settings[$s['setting_name']] = $s['setting_value'];
    }
    return $return_settings;
  }

}