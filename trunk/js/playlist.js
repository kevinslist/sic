var track_slow_dbl = new Object();

$(function(){
  applog('play-izzle: ' + home);
  applog('tracksss:');
  $('#layout-playlist').bind('center_resized', resize_playlist_area);
  resize_playlist_area();
  $( "#playlist-body" ).selectable({filter:'div.track', distance: 1 });
  
  $( "#playlist-body div.col").dblclick(track_double_clicked);
  $( "#playlist-body div.col").click(track_col_clicked);
  
});

function check_single_click(){
  if(track_slow_dbl.track_id){
    applog('slow--click:' + track_slow_dbl.track_id);
    sic_socket_send(track_slow_dbl.track_id);
    set_track_selected();
  }
}

function track_col_clicked(){
  var tid = $(this).parent().attr('data-track-id');
  var ct  = new Date().getTime();
  if(tid == track_slow_dbl.track_id && (ct - track_slow_dbl.click_time > 300) && (ct - track_slow_dbl.click_time < 600)){
    applog('slow--click:' + $(this).parent().attr('data-track-id'));
    track_slow_dbl.track_id   = false;
    track_slow_dbl.click_time = 0;
  }else{
    setTimeout('check_single_click();', 605);
    track_slow_dbl.track_id   = tid;
    track_slow_dbl.click_time = ct;
  }
}

function track_double_clicked(){
  set_track_selected();
  applog('dclicked:' + $(this).parent().attr('data-track-id'));
  return false;
}

function set_track_selected(){
  var track_id = track_slow_dbl.track_id;
  var row = $('#playlist-body > div.track[data-track-id=' + track_id + ']');
  var selected = $(row).hasClass('ui-selected');
  $('#playlist-body > div.track').removeClass('ui-selected');
  $(row).toggleClass('ui-selected', !selected);
  //applog('single-click:' + track_slow_dbl.track_id);
  track_slow_dbl.track_id   = false;
  track_slow_dbl.click_time = 0;
  $.get(home + 'player/goto/' + track_id);
}

function resize_playlist_area(){
  
  var hoffset = $('#playlist-body-wrapper').position();
  var new_height = $('#layout-playlist').height() - hoffset.top;
  //applog('plist-wrapper: ' + new_height);
  $('#playlist-body-wrapper').height( new_height);
  var pw = $('#playlist-scrollbar-fix').width();
  
  var rating_column = 50;
  var currently_playing_column = 18;
  pw = (pw - rating_column) - currently_playing_column;
  
  var title_column = .30 * pw;
  var artist_column = .15 * pw;
  var album_column = .25 * pw;
  
  var comment_column = pw - (title_column + artist_column + album_column);
  rating_column = rating_column - 6;
  
  
  $('#playlist-wrapper .currently-playing-column').width(currently_playing_column);
  $('#playlist-wrapper .title-column').width(title_column);
  $('#playlist-wrapper .artist-column').width(artist_column);
  $('#playlist-wrapper .album-column').width(album_column);
  $('#playlist-wrapper .comment-column').width(comment_column);
  $('#playlist-wrapper .rating-column').width(rating_column);
  
  applog('w:' + pw);
}