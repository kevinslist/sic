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
  return '<li class="artist-level" data-artist-id="">
            <div class="artist-name drag-to-playlist" data-menu-type="artist-level">' . $a['artist_name'] . '</div>
            <div class="album-list-wrapper">album list</div>
          </li>';
}