$(function(){
  applog('top-nizzle: ' + home);
  $('#layout-header .main-menu li').click(header_menu_clicked);
  $('div.mini-player-controls > div.progress > div.slider').slider({step: 1, change:control_seek_track});

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

var last_control_time = 0;
var last_control_length = 0;
var last_time_string = '0:00';

function control_seek_track(event,ui){
  if(event.originalEvent){
    var s = $('div.mini-player-controls > div.progress > div.slider').slider('option', 'value');
    //applog('SEEK: ' + s);
    sic_socket_send('seek', s);
  }
}

function update_controls(p){
  var is_playing = p.is_playing;
  var perc = 0;
  
  if(is_playing){
    $('div.mini-player-controls > div.toggle').text('pause');
    
    if(p.length != last_control_length){
      last_control_length = new Number(p.length);
      $('div.mini-player-controls > div.progress > div.slider').slider("option", "max", last_control_length);
      //applog('UCSETMAX:' + last_control_length);
    }
    
    if(p.time != last_control_time){
      last_control_time = new Number(p.time);
      
      //applog('UCSETVAL:' + last_control_time);
      var time = secs_time(last_control_time);
      last_time_string = time.m + ':' + time.s;
      var skip_slider = $('div.mini-player-controls > div.progress > div.slider > a.ui-slider-handle').hasClass('ui-state-active');
      
      if(!skip_slider){
        $('div.mini-player-controls > div.progress > div.slider').slider("option", "value", last_control_time);
      }
      
    }
  }else{
    $('div.mini-player-controls > div.toggle').text('play');
  }
  $('div.mini-player-controls > div.time').text(last_time_string);
}