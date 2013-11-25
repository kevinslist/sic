<?php

class library_settings_form extends my_form{

    public function render($form_title = null) {
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
                    'button-column' => array(
                        'width' => '6',
                        'col-style' => 'xs',
                        'children' => array(
                            'button' => 'button',
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
            'button' => array(
                'type' => 'button',
                'widget' => 'button',
                'label' => 'Import',
            ),
        );
    }

}