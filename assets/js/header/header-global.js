$(document).ready(sic_init_header);

function sic_init_header(){
  $('#layout-header .sic-menu-item').click(sic_header_menu_item_clicked);
}

function sic_header_menu_item_clicked(){
  var controller = $(this).attr('data-controller');
  $('#sic-menu-holder').load(sic_url(controller), sic_menu_loaded);
}

function sic_menu_loaded(){
  $('#sic-menu-holder').show();
  $('#sic-menu-holder #sic-menu-overlay-close-trigger').click(sic_overlay_menu_close_triggered);
}

function sic_overlay_menu_close_triggered(){
  $('#sic-menu-holder').empty();
  $('#sic-menu-holder').hide();
}