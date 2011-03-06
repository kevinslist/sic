var track_slow_dbl = new Object();
track_slow_dbl.click_time = 0;

$(function(){
  applog('play-izzle: ' + home);
  $('#layout-playlist').bind('center_resized', resize_playlist_area);
  //$( "#playlist-body" ).selectable({filter:'div.track', distance: 1 });
  //$( "#playlist-body div.col").dblclick(track_double_clicked);
  $('#playlist-load-area').load(home + 'playlist/load', null, playlist_loaded);
  resize_playlist_area();
});

function playlist_loaded(){
  $('#playlist-load-area div.col').click(track_col_clicked);
  $('#playlist-load-area div.track').draggable({helper:build_collection_menu_helper});
  resize_playlist_area();
}

function track_col_clicked(){
  var track_row = $(this).parent();
  var tid = $(track_row).attr('data-track-id');
  var ct  = new Date().getTime();
  applog('TID:'+ tid + "==" + track_slow_dbl.track_id);
  
  if(tid == track_slow_dbl.track_id && (ct - track_slow_dbl.click_time) < 900){
    
    if( (ct - track_slow_dbl.click_time) < 450){
      track_double_clicked($(this).parent().attr('data-track-id'));
      
    }else if( (ct - track_slow_dbl.click_time) < 900 ){
      track_slow_clicked($(this).parent().attr('data-track-id'));
    }
    track_slow_dbl.click_time = 0;
    track_slow_dbl.track_id = false;
    
  }else{
    
    track_slow_dbl.track_id   = tid;
    track_slow_dbl.click_time = ct;
    track_single_clicked();
  }
  
  return false;
}

function track_single_clicked(){
    applog('single-click:' + track_slow_dbl.track_id);
    set_track_selected();
}
function track_slow_clicked(){
  applog('slow--click:' + track_slow_dbl.track_id);
  return false;
}

function track_double_clicked(){
  sic_socket_send('play', track_slow_dbl.track_id );
  set_track_selected(1);
  return false;
}

function set_track_selected(do_selected){
  var track_id = track_slow_dbl.track_id;
  var row = $('#playlist-load-area div.track[data-track-id=' + track_id + ']');
  
  $('#playlist-load-area div.track').removeClass('ui-selected');
  var selected = $(row).hasClass('ui-selected');
  if(do_selected){
    selected = false;
  }
  $(row).toggleClass('ui-selected', !selected);
}

function resize_playlist_area(){
  
  var hoffset = $('#playlist-body-wrapper').position();
  var new_height = $('#layout-playlist').height() - hoffset.top;
  //applog('plist-wrapper: ' + new_height);
  $('#playlist-body-wrapper').height( new_height);
  var pw = $('#playlist-scrollbar-fix').width();
  
  var rating_column = 50;
  var currently_playing_column = 18;
  var length_column = 45;
  
  pw = (pw - rating_column) - currently_playing_column - length_column;
  
  var title_column = .25 * pw;
  var artist_column = .15 * pw;
  var album_column = .20 * pw;
  var genre_column = .15 * pw;
  
  var comment_column = pw - (title_column + artist_column + album_column + genre_column);
  
  rating_column = rating_column;
  length_column = length_column - 7;

  $('#playlist-wrapper .currently-playing-column').width(currently_playing_column);
  $('#playlist-wrapper .title-column').width(title_column);
  $('#playlist-wrapper .artist-column').width(artist_column);
  $('#playlist-wrapper .album-column').width(album_column);
  $('#playlist-wrapper .comment-column').width(comment_column);
  $('#playlist-wrapper .rating-column').width(rating_column);
  $('#playlist-wrapper .genre-column').width(genre_column);
  $('#playlist-wrapper .length-column').width(length_column);
  
}