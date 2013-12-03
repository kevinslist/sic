<?php

class settings_form extends my_form {
  public function render($form_title = null) {
    $form = parent::render($form_title);
    return $form;
  }

  function set_layout() {
    $this->layout = array(
        'row-1' => array(
            'type' => 'row',
            'columns' => array(
                'sic_lib_root-column' => array(
                    'width' => '12',
                    'children' => array(
                        'sic_lib_root' => 'sic_lib_root',
                    ),
                ),
            ),
        ),
    );
  }

  function set_field_definitions() {
    $this->field_definitions = array(
        'sic_lib_root' => array(
            'type' => 'text',
            'widget' => 'file_path',
            'label' => 'Lib Root',
        ),
    );
  }

}