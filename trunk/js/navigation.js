$(function(){
  applog('navgizzle: ' + home);
  $('#navigation-tabs').tabs();
  //$('#layout-header .main-menu li').click(header_menu_clicked);
  
  $('#application-navigation').bind('west_resized', resize_collection_menu_area);
  resize_collection_menu_area();
  
  $('div.drag-to-playlist').draggable({helper:build_collection_menu_helper});
  $('#layout-playlist').droppable({accept:'.drag-to-playlist', drop:add_to_playlist});
  $('.collection-menu-area li div.artist-letter').click(collection_menu_top_level_clicked);

  $('#collection-search').data('timeout', null).keyup(function(){
      clearTimeout($(this).data('timeout'));
      $(this).data('timeout', setTimeout(submit_collection_search, 800));
  });
  
});
var collection_searches = new Object();
collection_searches.empty = null;
collection_searches.searches = new Array();

function submit_collection_search(){
  var s = $('#collection-search').val();
  var temp = $('#collection-navigation-area div.collection-menu-area').detach();
  var id = $(temp).attr('data-search-key');
    
  if(id){
    applog('Cache-ID:' + id);
    collection_searches.searches[id] = temp;
  }else{
    collection_searches.empty = temp;
    applog('NO_CACHE-ID:' + id);
  }
    
  if('' == s){
    applog("empty search");
    if(collection_searches.empty){
      applog("def IS SET");
      $(collection_searches.empty).appendTo('#collection-search-load-area');
    }else{
      applog("def NOT SET");
      $(temp).appendTo('#collection-search-load-area');
    }
    resize_collection_menu_area();
  }else{
    
    if(collection_searches.searches[s]){
      applog('load cached search: ' + s);
      $(collection_searches.searches[s]).appendTo('#collection-search-load-area');
      resize_collection_menu_area();
    }else{
      applog('submit COL Search: ' + s);
      $('#collection-search-load-area').load(home + 'navigation/search', {'q':s}, collection_search_loaded);
    }
  }
}

function collection_search_loaded(){
  applog('searh results loaded');
}

function artist_list_loaded(text, status, xml){
  $('#collection-navigation-area .artist-list-menu.new-load').each(function(){
    $(this).find('li div.drag-to-playlist').draggable({helper:build_collection_menu_helper});
    $(this).find('li div.drag-to-playlist').click(load_artist_level_album_load);
    $(this).removeClass('new-load');
  });
}

function album_list_loaded(text, status, xml){
  $('#collection-navigation-area .album-list-menu.new-artist-load').each(function(){
    $(this).find('li div.drag-to-playlist').draggable({helper:build_collection_menu_helper});
    $(this).find('li div.drag-to-playlist').click(load_album_level_track_load);
    $(this).removeClass('new-artist-load');
  });
}

function track_list_loaded(text, status, xml){
  $('#collection-navigation-area .track-list-menu.new-album-load').each(function(){
    $(this).find('li div.drag-to-playlist').draggable({helper:build_collection_menu_helper});
    //$(this).find('li div.drag-to-playlist').click(load_album_level_track_load);
    $(this).removeClass('new-album-load');
  });
}




function do_album_level_track_load(tlwid, album_id){
  
    applog('load-tracks:' + album_id);
    $('#'+tlwid).load(home + 'navigation/album_level/' + album_id,
                                                        null,
                                                        track_list_loaded);
}
function load_album_level_track_load(){
  $(this).parent().find('.track-list-wrapper').toggle();
  
  if($(this).parent().find('.track-list-wrapper').children().length){
    applog('ALBs already loaded: ' + $(this).parent().attr('data-album-id'));
  }else{
    var tlwid     = $(this).parent().find('.track-list-wrapper').attr('id');
    var album_id   = $(this).parent().attr('data-album-id');
    setTimeout('do_album_level_track_load("' + tlwid  + '", "' + album_id + '")', 10);
  }
}

function do_artist_level_album_load(alblwid, artist_id){
  
    applog('load-albums:' + artist_id);
    $('#'+alblwid).load(home + 'navigation/artist_level/' + artist_id,
                                                        null,
                                                        album_list_loaded);
}
function load_artist_level_album_load(){
  $(this).parent().find('.album-list-wrapper').toggle();
  
  if($(this).parent().find('.album-list-wrapper').children().length){
    applog('ALBs already loaded: ' + $(this).parent().attr('data-artist-id'));
  }else{
    var alblwid     = $(this).parent().find('.album-list-wrapper').attr('id');
    var artist_id   = $(this).parent().attr('data-artist-id');
    setTimeout('do_artist_level_album_load("' + alblwid  + '", "' + artist_id + '")', 10);
  }
}

function collection_menu_top_level_clicked(){
  $(this).parent().find('.artist-list-wrapper').toggle();
  
  if($(this).parent().find('.artist-list-wrapper').children().length){
    applog('already loaded: ' + $(this).parent().attr('data-letter'));
  }else{
    var alwid       = $(this).parent().find('.artist-list-wrapper').attr('id');
    var data_letter = $(this).parent().attr('data-letter');
    setTimeout('do_top_level_arist_load("' +alwid  + '", "' + data_letter + '")', 10);
  }
}

function do_top_level_arist_load(alwid, alwl){
    applog('load-letter' + alwid + "::" + alwl);
  
    $('#'+alwid).load(home + 'navigation/top_level/' + alwl,
                                                        null,
                                                        artist_list_loaded);
  /*
    $(this).parent().find('.artist-list-wrapper').load(home + 'navigation/top_level/' + $(this).parent().attr('data-letter'),
                                                        null,
                                                        artist_list_loaded);
  */
}

function add_to_playlist(event,ui){
  applog('dropped: ' + $(ui.draggable).attr('data-menu-type'));
}

function build_collection_menu_helper(){
  return $('<div class="collection-menu-helper">kevin</div>').appendTo('body').css('zIndex',5).show();
}

function resize_collection_menu_area(){
  var hoffset = $('#application-navigation').find('.collection-menu-area').position();
  
  $('#application-navigation').find('.collection-menu-area').height( $('#application-navigation').height() - hoffset.top);
}

function navigation_menu_clicked(){
  
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