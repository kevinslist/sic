var home = '/';

$(function(){
 //alert(home+'topnav');
  //$.get(home+'collection/scan', applog);
  //$('#layout-footer').load(home+'footer');
  //$('#layout-playlist').load(home+'playlist');
  //$('#content').load(home+'content');

  applog('page-load: ' + home);
  $('#layout-header').load(home+'header');
});

function applog(mess){
  $('#app-logger').prepend( $('<p>SYS: '+mess+'</p>' ));
}

function execute_action(action){
  
    
  
}