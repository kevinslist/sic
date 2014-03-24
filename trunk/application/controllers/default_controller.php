<?php

class default_controller extends my_controller {
  public function home(){
    die(kb::view('home/logged_in'));
  }
}