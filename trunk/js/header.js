$(function(){
  applog('top-nizzle: ' + home);
  $('#layout-header .main-menu li').click(header_menu_clicked);
  
});

function header_menu_clicked(){
  
  var menuid = $(this).attr('data-menuid');
  switch(menuid){
    case('collection_scanner'):
      new_window = window.open(home + 'collection_scanner/menu','collection-scanner-menu','scrollbars=no,resizable=no,toolbar=no,location=no,width=400,height=200');
      new_window.moveTo( (screen.width / 2 - 200), (screen.height / 2 - 100));
      new_window.focus();
      break;
    default:
       alert(menuid);
      // do nothing
      break;
  }
  
  return false;
}