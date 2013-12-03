$(document).ready(sic_init_settings_library_menu);

function sic_init_settings_library_menu(){
  $('#import_button').click(sic_library_begin_import);
}

function sic_library_begin_import(){
  $.get(sic_url('library/import'));
  return false;
}