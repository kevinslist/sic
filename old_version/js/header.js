$(function(){
  applog('top-nizzle: ' + home);
  $('#layout-header .main-menu li').click(header_menu_clicked);
  $('div.mini-player-controls > div.progress > div.slider').slider({
    step: 1, 
    change:control_seek_track
  });
  $('div.mini-player-controls > div.toggle').click(control_toggle_playing);
  $('div.mini-player-controls > div.next').click(control_next_track);
  $('div.mini-player-controls > div.previous').click(control_prev_track);

});

function control_prev_track(){
  applog('PREV TRACK CLICKED:');
  sic_socket_send('prev_track', $('#playlist-load-area div.current-playlist-id').attr('data-playlist-id'));
}
function control_next_track(){
  applog('NEXT TRACK CLICKED:');
  sic_socket_send('next_track', $('#playlist-load-area div.current-playlist-id').attr('data-playlist-id'));
}

function header_menu_clicked(){
  
  var tab = $(this).attr('id');
  switch(tab){
    case('quit-tab'):
      applog('QUIT:'+tab);
      control_app_quit();
      $.get(home + 'process/quit');
      break;
    case('collection-scan-tab'):
    default:
      new_window = window.open(home + 'menu#'+tab,'main-menu');
      new_window.focus();
      break;
  }
  
  return false;
}

var last_control_time = 0;
var last_control_length = 0;
var last_time_string = '0:00';
var playing_status  = 0;
var control_time_handler = 0;


function control_app_quit(){
  applog('quit called header');
  $('div.mini-player-controls').html( $('<div>Quit</div>') );
}

function control_toggle_playing(){
  sic_socket_send('toggle', 'toggle');
}

function control_seek_track(event,ui){
  if(event.originalEvent){
    var s = $('div.mini-player-controls > div.progress > div.slider').slider('option', 'value');
    //applog('SEEK: ' + s);
    sic_socket_send('seek', s);
  }
}

function update_current_time(p){
  //applog(playing_status + '::||update_current_time: ' + last_control_time);
  if(playing_status == 2){
    if(p){
      last_control_time = new Number(p.time);
    }else{
      last_control_time = last_control_time + 1;
    }
    update_time_and_slider(last_control_time);
    clearTimeout(control_time_handler);
    control_time_handler = setTimeout('update_current_time();', 1000);
  }
}

function update_time_and_slider(t){
  var time = secs_time(t);
    
  last_time_string = time.m + ':' + time.s;
  var skip_slider = $('div.mini-player-controls > div.progress > div.slider > a.ui-slider-handle').hasClass('ui-state-active');
  if(!skip_slider){
    $('div.mini-player-controls > div.progress > div.slider').slider("option", "value", t);
  }
  $('div.mini-player-controls > div.time').text(last_time_string);
}

function update_controls(p){
  //applog('update_controls1: ' + p.is_playing);
  playing_status = new Number(p.is_playing);
  //applog('update_controls2: ' + p.is_playing);
  
  if(playing_status > 0){
    
    if(2 == playing_status){
      $('div.mini-player-controls > div.toggle').text('pause');
    }else{
      $('div.mini-player-controls > div.toggle').text('play2');
      update_time_and_slider( new Number(p.time));
    }
    if(p.length != last_control_length){
      last_control_length = new Number(p.length);
      $('div.mini-player-controls > div.progress > div.slider').slider("option", "max", last_control_length);
    }
    clearTimeout(control_time_handler);
    update_current_time(p);
      
  }else{
    $('div.mini-player-controls > div.toggle').text('play');
  }
}