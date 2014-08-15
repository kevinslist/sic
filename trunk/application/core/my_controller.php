<?php

if (!defined('BASEPATH')) {
  exit('No direct script access allowed');
}

class my_controller extends kb_controller {
  var $kb_content;
  public function __construct() {
    parent::__construct('sic-bootstrap-1');
    $this->force_login = TRUE;
    $this->config->load('assets');
    $this->load->database();
    $this->load->helper('url');
    $this->load->model('client');
    $this->kb_content = array();
  }

  public function index() {
    $this->kb_content['global-main-menu'] = kb::view('global/main-menu');
    $this->render_page();
  }

  public function render_page($content = NULL) {
      if(!empty($content)){
          if(is_array($content)){
            $this->kb_content = array_merge($this->kb_content, $content);
          }else{
            $this->kb_content['default'] = $content;
          }
      }
    parent::render_page(implode("\r\n", $this->kb_content));
  }

  public function login($method = NULL, $params = NULL) {
    $uri_parts = explode('/', uri_string());
    $kb_func = current($uri_parts);

    if ($kb_func == 'check-registration') {
      $this->check_registration();
    } else {
      $css_array = array(
          'assets/css/sic-basic.css',
      );
      $css = kb::view('assets/css', array('css_files' => $css_array));
      $js = array(
          'assets/kb/templates/sic-bootstrap-1/lib/jquery/1.10/js/jquery.js',
          'assets/kb/templates/sic-bootstrap-1/js/kb-auth.js',
          'assets/js/sic-login.js'
      );
      $js = kb::view('assets/js', array('js_files' => $js));
      $vars = array('kb_auth_type' => 'login');
      $content = kb::view('snippets/kb-auth-block', $vars);
      $this->page_title('SIC Sign In');
      print kb::view('layouts/default_layout', array('content' => $content, 'css' => $css, 'js' => $js));
    }
  }

  function check_registration() {
    $response = array(
        'status' => false,
    );
    if ($this->ajax_call && !empty($_POST['access_token']) && !$this->client->logged_in() && isset($_POST['provider'])) {

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
      if ($this->client->status == 'active') {
        $response['status'] = true;
      }
    }
    print json_encode($response);
  }

  function _remap($method = NULL, $params = NULL) {
    //kb::dump($this->client);
    if ($this->force_login && !$this->client->logged_in()) {
      $this->login($method, $params);
    } else {
      parent::_remap($method, $params);
    }
  }

}