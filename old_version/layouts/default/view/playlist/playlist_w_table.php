<?php
/*
  <audio controls autobuffer autoplay src="playsong/<?php print urlencode($r);?>"></audio>
  <a href="get.php?i=<?php print urlencode($r);?>">get.php?i=<?php print urlencode($r);?></a>
 */
?>
<div class="playlist-wrapper">
  <div class="playlist-header-wrapper">
    <table class="playlist-header" cellspacing="0">
      <thead>
        <tr>
          <th class="title-column">Title</th>
          <th class="artist-column">Artist</th>
          <th class="album-column">Albumn</th>
          <th class="comment-column">Comment</th>
          <th class="rating-column">Rating</th>
        </tr>
      </thead>
    </table>
  </div>
  <div class="playlist-table-wrapper">
    <table class="playlist-table" cellspacing="0">
      <tbody>
        <?php
        $songs = array();
        for($j=0;$j<40;$j++) {
          $c = $j % 2 ? 'o' : 'e';
          print '<tr class="' . $c . '"><td class="title-column"><span>&nbsp;</span></td>';
          print '<td class="artist-column"><span>&nbsp;</span></td>';
          print '<td class="album-column"><span>&nbsp;</span></td>';
          print '<td class="comment-column"><span>&nbsp;</span></td>';

          print '<td class="rating-column"><span>&nbsp;</span></td></tr>';
        }
        ?>
      </tbody>
    </table>
    <div id="playlist-padder"></div>
  </div>
</div>