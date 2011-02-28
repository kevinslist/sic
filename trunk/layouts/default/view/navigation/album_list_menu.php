<div class="collection-album-list-area">
  <ul class="album-list-menu new-artist-load">
    <?php 
    foreach($album_list['albums'] as $k=>$a){
      print artist_search::build_album_level_menu_albums($a);
    }
    foreach($album_list['tracks'] as $k=>$a){
      print artist_search::build_album_level_menu_tracks($a);
    }
    ?>
  </ul>
</div>
