<div class="collection-album-list-area">
  <ul class="album-list-menu new-artist-load">
    <?php 
    foreach($track_list as $k=>$a){
      print artist_search::build_album_level_menu_tracks($a);
    }
    ?>
  </ul>
</div>
