<?php

class settings_controller extends my_controller {

  public function index() {
    $assets = array(kb::icss('settings/settings-menu'), kb::iscript('settings/settings-menu'));
    $vars = array('assets' => implode("\r\n", $assets));
    $settings_form = new settings_form();
    $settings_form->settings(kb::db_get('settings'));
    $vars['content'] = $settings_form->render();
    die(kb::view('menus/overlay', $vars));
  }
  public function library() {
    $assets = array(kb::icss('settings/library-menu'), kb::iscript('settings/library-menu'));
    $vars = array('assets' => implode("\r\n", $assets));
    $library_settings_form = new library_settings_form();
    $library_settings_form->settings(kb::db_get('settings'));
  
    $vars['content'] = $library_settings_form->render();
    die(kb::view('menus/overlay', $vars));
  }

}