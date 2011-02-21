<div id="collection-default-menu" class="collection-menu-area">
  <ul id="artist-sort-menu">
    <?php 
    for($i=65;$i<91;$i++){
      print build_top_level_menu(chr($i));
    }
    for($i=0;$i<10;$i++){
      print build_top_level_menu($i);
    }
    print build_top_level_menu('Other');
    ?>
  </ul>
</div>

<?php
$artist_list_wrapper_id = 0;

function build_top_level_menu($text){
  global $artist_list_wrapper_id;
  $artist_list_wrapper_id++;
  
  return '<li class="top-level" data-letter="' . $text . '">
            <div class="artist-letter drag-to-playlist" data-menu-type="top-level">' . $text . '</div>
            <div id="alw' . $artist_list_wrapper_id . '" class="artist-list-wrapper">loading...</div>
          </li>';
}