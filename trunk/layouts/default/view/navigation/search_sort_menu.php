<div data-search-key="<?= $query ?>" class="collection-menu-area">
  <ul id="artist-sort-menu">
    <?php
    foreach ($data as $k => $v) {
      $top_level = build_top_level_menu($k);
      $all_artists = '';
      
      foreach ($v as $artist_id => $artist_album_array) {
        $artist_name = get_artist_name($artist_album_array);
        $tracks = isset($artist_album_array['alb0']) ? $artist_album_array['alb0'] : array();
        unset($artist_album_array['alb0']);
        
        $top_track_list = '';
        foreach($tracks as $t){
          $top_track_list .= artist_search::build_album_level_menu_tracks($t);
        }
        
        $album_list = '';
        foreach($artist_album_array as $a){
          $album_list .= build_album_level_menu_albums($a);
        }
        
        $artist_info        = array('artist_id'=>'', 'artist_name'=>$artist_name, 'album_list'=>$album_list, 'top_track_list'=>$top_track_list);
        $artist_level_menu  = build_artist_level_menu($artist_info);
        
        $all_artists .= $artist_level_menu;
      }
      printf($top_level, $all_artists);
  
    }
    ?>
  </ul>
</div>

<?php


function build_artist_level_menu($a) {
  return '<div class="collection-artist-list-area">
  <ul class="artist-list-menu new-load">
  <li class="artist-level" data-artist-id="'. $a['artist_id'] .'">
            <div class="artist-name drag-to-playlist" data-menu-type="artist-level">'. $a['artist_name'] .'</div>
            <div class="album-list-wrapper" id="'. 'artid' . $a['artist_id'] . '">
              
    <div class="collection-album-list-area">
  <ul class="album-list-menu new-artist-load">
  ' . $a['album_list'] . $a['top_track_list'] . '
  </ul>
  </div>
            </div>
          </li>
     </ul>
     </div>';
  // . $a['top_track_list']

}

  function build_album_level_menu_albums($a){
    $alb = null;
    $track_list = '';
    foreach($a as $t){
      $track_list .= artist_search::build_album_level_menu_tracks($t);
      if(empty($alb)){
        $alb = array('album_id'=>current($t['album_id']), 'album_name'=> current($t['album_name']));
        
      }
    }
    
    if(!empty($alb)){
      $alblist = sprintf('<li class="album-level" data-album-id="%s">
              <div class="album-name drag-to-playlist" data-menu-type="album-level">%s</div>
              <div class="track-list-wrapper" id="%s">
                <div class="collection-album-list-area">
                  <ul class="track-list-menu new-album-load">
                    %s
                  </ul>
                </div>
              </div>
            </li>', $alb['album_id'], $alb['album_name'], 'albid'.$alb['album_id'], $track_list);
    }
    return $alblist;
  }


$artist_list_wrapper_id = 0;

function build_top_level_menu($text) {
  global $artist_list_wrapper_id;
  $artist_list_wrapper_id++;

  return '<li class="top-level" data-letter="' . $text . '">
            <div class="artist-letter drag-to-playlist" data-menu-type="top-level">' . $text . '</div>
            <div id="alw' . $artist_list_wrapper_id . '" class="artist-list-wrapper">%s</div>
          </li>';
}


function get_artist_name($artist_array){
  $an = '';
  $t  = current(current($artist_array));
  
  if(!empty($t) && isset($t['artist_name']) && is_array($t['artist_name'])){
    $an = current($t['artist_name']);
  }
  if(empty($an)){
    $an = 'Unknown';
  }
  return $an;
}