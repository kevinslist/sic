<div class="current-playlist-id" data-playlist-id="<?=$playlist_id?>"></div>
<?php 
$assoc_rec  = 0;
$wrapper = '<div class="track %s" data-track-id="%s">
  <div class="col currently-playing-column"><div></div></div>
  <div class="col title-column"><div>%s</div></div>
  <div class="col artist-column"><div>%s</div></div>
  <div class="col album-column"><div>%s</div></div>
  <div class="col comment-column"><div>%s</div></div>
  <div class="col genre-column"><div>%s</div></div>
  <div class="col rating-column" %s><div>%s</div></div>
  <div class="col length-column"><div>%s</div></div>
  <div class="clear"></div>
  </div>';
foreach($tracks as $k=>$t){
  $class = $assoc_rec%2 == 0 ? 'o' : 'e';
  $tid = (int)$k;
  printf($wrapper, $class, $tid, 
          htmlentities($t['track_title']),
          isset($t['artist_name']) ? htmlentities(implode(' | ', $t['artist_name'])) : '',
          isset($t['album_name']) ? htmlentities(implode(' | ', $t['album_name'])) : '',
          isset($t['track_comment']) ? htmlentities(implode(' | ', $t['track_comment'])) : '',
          isset($t['genre_name']) ? htmlentities(implode(' | ', $t['genre_name'])) : '',
          (int)$t['popm_rating'] ? 'style="background-color: #' . dechex((int)$t['popm_rating']) . '0000;"'  : '',
          htmlentities($t['default_rating']),
          htmlentities($t['track_length'])

          );
  $assoc_rec++;
}
