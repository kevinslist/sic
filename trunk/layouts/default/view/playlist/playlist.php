<div id="playlist-wrapper">
  <div id="playlist-header-wrapper">
    <ul id="playlist-header">
      <li class="currently-playing-column"></li>
      <li class="title-column"><div>Title</div></li>
      <li class="artist-column"><div>Artist</div></li>
      <li class="album-column"><div>Albumn</div></li>
      <li class="comment-column"><div>Comment</div></li>
      <li class="rating-column"><div>Rating</div></li>
    </ul>
    <div class="clear"></div>
  </div>
  <div id="playlist-body-wrapper">
<?php
  $assoc_rec  = 0;
  /*
  $title      = '';
  $artist     = '';
  $album      = '';
  $comment    = '';
  $rating     = '';
  $wrapper = '<div class="%s" data-track-id="%s">%s</div>';
  foreach($tracks as $k=>$t){
    $class = $assoc_rec%2 == 0 ? 'o' : 'e';
    $tid = (int)$k;
    //$title    .= '<div class="'.$class.'">' . $t['track_title'] . '</div>';
    
    $title    .= sprintf($wrapper,$class,$tid, $t['track_title']);
    $artist   .= sprintf($wrapper,$class,$tid, implode(' | ', $t['artist_name']));
    $album    .= sprintf($wrapper,$class,$tid, implode(' | ', $t['album_name']));
    $comment  .= sprintf($wrapper,$class,$tid, implode(' | ', $t['track_comment']));
    $rating   .= sprintf($wrapper,$class,$tid, $t['default_rating']);
    $assoc_rec++;
  }
   * 
   */
?>
    <div id="playlist-scrollbar-fix">
      <div id="playlist-body">
        <?php 
        $assoc_rec  = 0;
        $wrapper = '<div class="track %s" data-track-id="%s">
          <div class="col currently-playing-column"><div></div></div>
          <div class="col title-column"><div>%s</div></div>
          <div class="col artist-column"><div>%s</div></div>
          <div class="col album-column"><div>%s</div></div>
          <div class="col comment-column"><div>%s</div></div>
          <div class="col rating-column"><div>%s</div></div>
          <div class="clear"></div>
          </div>';
        foreach($tracks as $k=>$t){
          $class = $assoc_rec%2 == 0 ? 'o' : 'e';
          $tid = (int)$k;
          printf($wrapper, $class, $tid, 
                  htmlentities($t['track_title']),
                  htmlentities(implode(' | ', $t['artist_name'])),
                  htmlentities(implode(' | ', $t['album_name'])),
                  htmlentities(implode(' | ', $t['track_comment'])),
                  htmlentities($t['default_rating'])
                  
                  );
          $assoc_rec++;
        }
       /* <li class="title-column"><?=$title?><div class="clear"></div></li>
        <li class="artist-column"><?=$artist?><div class="clear"></div></li>
        <li class="album-column"><?=$album?><div class="clear"></div></li>
        <li class="comment-column"><?=$comment?><div class="clear"></div></li>
        <li class="rating-column"><?=$rating?><div class="clear"></div></li>
        * 
        */
        ?>
      <div class="clear"></div>
    </div>
    
  </div>
</div>