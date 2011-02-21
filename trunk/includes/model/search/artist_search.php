<?php

class artist_search {

  static function artist_menu_list($letter) {
    $artists = array();

    if (preg_match('`^[A-Z]{1}$`', $letter)) {
      $artists = db::query('SELECT * FROM artists WHERE artist_name LIKE ? or artist_name LIKE ? ORDER BY artist_name ASC', array($letter . '%', strtolower($letter) . '%'));
    } elseif (preg_match('`^[0-9]{1}$`', $letter)) {
      $artists = db::query('SELECT * FROM artists WHERE artist_name LIKE ? ORDER BY artist_name ASC', array($letter . '%'));
    } else {
      $artists = db::query("SELECT * FROM artists WHERE artist_name REGEXP '^[^a-zA-Z0-9]' ORDER BY artist_name ASC");
    }

    return $artists;
  }

}
