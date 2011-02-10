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
        foreach ($songs as $s) {
          $i++;
          $c = $i % 2 ? 'o' : 'e';
          print '<tr class="' . $c . '"><td class="title-column"><span>' . $s['title'] . '</span></td>';
          print '<td class="artist-column"><span>' . $s['artist'] . '</span></td>';
          print '<td class="album-column"><span>' . $s['album'] . '</span></td>';
          print '<td class="comment-column"><span>' . $s['comment'] . '</span></td>';

          print '<td class="rating-column"><span>' . $s['rating'] . '</span></td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>