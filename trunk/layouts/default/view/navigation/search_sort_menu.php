<div data-search-key="<?=$query?>" class="collection-menu-area">
  <ul id="artist-sort-menu">
    <?php 
    foreach($data as $k=>$v){
      print build_top_level_menu($k);
    }
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