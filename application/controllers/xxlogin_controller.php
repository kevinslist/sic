<?php

class login_controller extends my_controller {
  public function index(){
    $this->login();
  }
  public function login(){
    
    die('login called2');
  }
  public function check_registration() {
    $url = site_url();
    if (!$this->client->logged_in() && isset($_POST['provider'])) {
      switch ($_POST['provider']) {
        case 'google':
          $access_token = !empty($_POST['access_token']) ? $_POST['access_token'] : NULL;
          if (!empty($access_token)) {
            $google_url = 'https://www.googleapis.com/plus/v1/people/me?access_token=' . $access_token;
            $json = kb::curl($google_url);
            if (!empty($json)) {
              $json_decoded = json_decode($json, TRUE);
              if (is_array($json_decoded) && !empty($json_decoded['id'])) {
                $json_decoded['provider'] = 'google';
                $json_decoded['google_id'] = $json_decoded['id'];
                $this->client->load($json_decoded);
              }
            }
          }
          break;
        case 'facebook':
          $access_token = !empty($_POST['access_token']) ? $_POST['access_token'] : NULL;
          if (!empty($access_token)) {
            $facebook_url = 'https://graph.facebook.com/me?access_token=' . $access_token;
            $json = kb::curl($facebook_url);
            if (!empty($json)) {
              $json_decoded = json_decode($json, TRUE);
              if (is_array($json_decoded) && !empty($json_decoded['id'])) {
                $json_decoded['provider'] = 'facebook';
                $json_decoded['facebook_id'] = $json_decoded['id'];
                $this->client->load($json_decoded);
              }
            }
          }
          break;
      }
    }
    switch ($this->client->status) {
      case 'active':
        $url .= 'home';
        break;
      case 'logged_in':
        $url .= 'unknown';
        break;
      case 'guest':
        $url .= 'guest';
        break;
    }
    die(json_encode(array('url' => $url)));
  }

}