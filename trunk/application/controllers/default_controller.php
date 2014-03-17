<?php

class default_controller extends my_controller {
  public function home(){
    $vars = array('google_signin_block'=>kb::view('snippets/google-signin-block'));
    $content = $this->client->logged_in() ? kb::view('home/logged_in', $vars) : kb::view('home/logged_out', $vars);
    $this->render_page($content);
  }
}