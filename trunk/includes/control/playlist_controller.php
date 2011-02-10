<?php
  $start = (int)rand(0,222);
  $songs = amarok::get($start,422);
  $merged = array();
  $i=0;
  $r = current($songs);
  $r = substr($r['url'],1);

	$app->css('playlist.css');
	$app->js('playlist.js');
  include $app['view'] . 'playlist.php';