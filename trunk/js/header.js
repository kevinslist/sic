$(function(){
  applog('top-nizzle: ' + home);
  $('#layout-header .main-menu li').click(header_menu_clicked);
  
});

function header_menu_clicked(){
  
  var tab = $(this).attr('id');
  switch(tab){
    case('collection-scan-tab'):
      new_window = window.open(home + 'collection_scanner/menu','collection-scanner-menu','scrollbars=no,resizable=no,toolbar=no,location=no,width=400,height=200');
      new_window.moveTo( (screen.width / 2 - 200), (screen.height / 2 - 100));
      new_window.focus();
      break;
    case('quit-tab'):
       applog('QUIT:'+tab);
       $.get(home + 'process/quit');
      break;
    default:
       applog('MENUTABCLICKED:'+tab);
      break;
  }
  
  return false;
}