<?php

class library_settings_form extends my_form {

  public function render($form_title = null) {
    $this->ajax_form = TRUE;
    $form = parent::render($form_title);
    return $form;
  }

  function set_layout() {
    $this->layout = array(
        'row-1' => array(
            'type' => 'row',
            'columns' => array(
                'import-column' => array(
                    'width' => '6',
                    'col-style' => 'xs',
                    'children' => array(
                        'current_import_path' => 'current_import_path',
                    ),
                ),
                'import_button-column' => array(
                    'width' => '6',
                    'col-style' => 'xs',
                    'children' => array(
                        'import_button' => 'import_button',
                    ),
                ),
            ),
        ),
        'row-2' => array(
            'type' => 'row',
            'columns' => array(
                'scan-column' => array(
                    'width' => '12',
                    'col-style' => 'xs',
                    'children' => array(
                        'scan_button' => 'scan_button',
                    ),
                ),
            ),
        ),
    );
  }

  function set_field_definitions() {
    $this->field_definitions = array(
        'current_import_path' => array(
            'type' => 'text',
            'widget' => 'file_path',
            'label' => 'import',
        ),
        'import_button' => array(
            'type' => 'button',
            'widget' => 'button',
            'label' => 'Import',
        ),
        'scan_button' => array(
            'type' => 'button',
            'widget' => 'button',
            'label' => 'Start Scan',
        ),
    );
  }

}