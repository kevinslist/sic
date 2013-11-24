<?php

class settings_controller extends my_controller {

  public function index() {
    $assets = array(kb::icss('settings/settings-menu'), kb::iscript('settings/settings-menu'));
    $vars = array('assets' => implode("\r\n", $assets));
    $vars['content'] = $settings_form->render();
    kb::view('menus/overlay', $vars);
  }

}