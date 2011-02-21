<?php
 
class tracks_controller extends controller{
  function __construct(){
    
  }
  function control(){
    switch(app::next()){
      case('all'):
        ini_set('memory_limit', '300m');
        $popm_default = settings::val('popm_email_default');
        
        $rs = db::query('SELECT tp.popm_rating, tp.popm_counter, t.*, a.*,al.*,tal.track_number,g.genre_name
FROM tracks t
LEFT JOIN track_artist ta ON t.track_id = ta.track_id
LEFT JOIN artists a ON ta.artist_id = a.artist_id
left join track_genre tg ON t.track_id = tg.track_id
LEFT join genres g ON tg.genre_id = g.genre_id
left join track_album tal ON t.track_id = tal.track_id
LEFT JOIN albums al ON tal.album_id = al.album_id
left join track_popm tp ON t.track_id = tp.track_id and tp.popm_email = ?
ORDER BY t.track_id, ta.track_artist_order, tal.track_album_order, tg.track_genre_order', $popm_default);
        
        $oid = 0;
        $tracks = array();
        $new_track = null;
        
        foreach($rs as $r){
          $tid = $r['track_id'];
          if($tid != $oid){
            if(!empty($new_track)){
              $tracks[] = $new_track;
            }
            $new_track = array();
            $new_track['track_id']      = $tid;
            $new_track['track_title']   = $r['track_title'];
            $new_track['track_length']  = $r['track_length'];
            $new_track['popm_rating']   = $r['popm_rating'];
            
          }
          $oid = $tid;
        }
        $tracks[] = $new_track;
        
        print json_encode($tracks);
        break;
      default:
        print json_encode('get the tracks!');
        break;
    }
  }
}