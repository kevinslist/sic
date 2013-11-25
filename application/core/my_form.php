<?php

class my_form extends kb_form{
  public function __construct($form_id = null, $form_unique_id = null, $primary_key = null, $is_unique_extra = '', $sub_form = FALSE) {
    parent::__construct($form_id, $form_unique_id, $primary_key, $is_unique_extra, $sub_form);
  }
  public function render_buttons($buttons = null) {
    return '';
  }
  
}