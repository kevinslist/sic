<?php

switch($app->next()){
	default:
		$app->css('content.css');
		$app->js('content.js');
		include $app['view'] . 'content.php';
		break;
}
	
	
