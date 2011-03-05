<?php

class track_search {

  static function info($track_id) {
    $popm_default = settings::val('popm_email_default');
    $rs = db::query(self::$sql1 . ' WHERE t.track_id = ? ', array($popm_default, $track_id));
    return current(self::merge($rs));
  }

  static function merge($rs) {
    // 6500
    $oid = 0;
    $tracks = array();
    $new_track = null;

    foreach ($rs as $r) {
      $tid = $r['track_id'];

      if (!isset($tracks[$tid])) {
        $new_track = array();
        $rating = round(round((int) $r['default_rating'] / 255 * 10000) * .1) * .1;
        $rating = $rating ? $rating : '';
        $new_track['track_id'] = $tid;
        $new_track['track_title'] = $r['track_title'];
        $new_track['track_length'] = $r['track_length'];
        $new_track['default_rating'] = $rating;
        $new_track['popm_rating'] = $r['default_rating'];
        $new_track['default_counter'] = $r['default_counter'];
        $new_track['track_year'] = $r['track_year'];
        $new_track['bit_rate'] = $r['bit_rate'];
        $new_track['sample_rate'] = $r['sample_rate'];
        $new_track['file_size'] = $r['file_size'];
        $new_track['mime_type'] = $r['mime_type'];
        $new_track['file_format'] = $r['file_format'];
      } else {
        $new_track = $tracks[$tid];
      }
      if(isset($r['artist_id'])){
        $new_track['artist_id']['aid' . $r['artist_id']] = $r['artist_id'];
        $new_track['artist_name']['an' . $r['artist_id']] = $r['artist_name'];
        $new_track['artist_description']['ad' . $r['artist_id']] = $r['artist_description'];
      }
      if(isset($r['track_comment_id'])){
        $new_track['track_comment']['c' . $r['track_comment_id']] = $r['track_comment'];
      }
      
      if(isset($r['album_id'])){
        $new_track['album_id']['alid' . $r['album_id']] = $r['album_id'];
        $new_track['album_name']['aln' . $r['album_id']] = $r['album_name'];
        $new_track['track_number']['altn' . $r['album_id']] = $r['track_number'];
      }
      if(isset($r['genre_name'])){
        $new_track['genre_name'][$r['genre_name']] = $r['genre_name'];
      }
      
      if (!isset($tracks[$tid])) {
        $tracks[$tid] = $new_track;
      }
    }

    return $tracks;
  }

  function random($count = 100) {

    ini_set('memory_limit', '300m');
    $popm_default = settings::val('popm_email_default');
    $limit = (int) $count > 0 ? (int)$count : 40000;
    $random_sql = self::$sql1 . '  WHERE tp.popm_rating > 40 ';
    $rs = db::query($random_sql . ' ORDER BY RAND() LIMIT ' . $count, $popm_default);
    //WHERE a.artist_name_index LIKE "%layer%"
    //ORDER BY 
    //t.track_id, ta.track_artist_order, 
    //tal.track_album_order, tg.track_genre_order,
    //tc.track_comment_order LIMIT 0, ' . $limit
    return self::merge($rs);
  }

  static $sql1 = 'SELECT 
                  t.*, a.*,al.*,tal.track_number,g.genre_name, tc.track_comment_id, tc.track_comment,
                  tp.popm_rating as default_rating, tp.popm_counter as default_counter
                      FROM tracks t
                      LEFT JOIN track_artist ta ON t.track_id = ta.track_id
                      LEFT JOIN artists a ON ta.artist_id = a.artist_id
                      LEFT JOIN track_genre tg ON t.track_id = tg.track_id
                      LEFT JOIN genres g ON tg.genre_id = g.genre_id
                      LEFT JOIN track_album tal ON t.track_id = tal.track_id
                      LEFT JOIN albums al ON tal.album_id = al.album_id
                      LEFT JOIN track_comments tc ON t.track_id = tc.track_id
                      LEFT JOIN track_popm tp ON t.track_id = tp.track_id AND tp.popm_email = ?
                      ';
  
  
    //WHERE a.artist_name_index LIKE "%layer%"
    //ORDER BY 
    //t.track_id, ta.track_artist_order, 
    //tal.track_album_order, tg.track_genre_order,
    //tc.track_comment_order LIMIT 0, ' . $limit

}