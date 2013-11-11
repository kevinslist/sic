<div class="collection-artist-list-area">
  <ul class="artist-list-menu new-load">
    <?php 
    foreach($artist_list as $a){
      print build_artist_level_menu($a);
    }
    ?>
  </ul>
</div>

<?php
function build_artist_level_menu($a){
  return sprintf('<li class="artist-level" data-artist-id="%s">
            <div class="artist-name drag-to-playlist" data-menu-type="artist-level">%s</div>
            <div class="album-list-wrapper" id="%s">album list loading...</div>
          </li>', $a['artist_id'], $a['artist_name'], 'artid'.$a['artist_id']);
  //' . $a['artist_name'] . '
}