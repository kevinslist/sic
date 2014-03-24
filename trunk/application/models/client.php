<?php

class client extends kb_client{
  
  public function logged_in($set_logged_in = NULL) {
    $return = $this;
    if (is_null($set_logged_in)) {
      $return = 'active' == $this->status;
    } else {
      if (FALSE === $set_logged_in) {
        $this->log_out();
      }
    }
    return $return;
  }
}