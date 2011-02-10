<?php

switch($app->next()){
	default:
		$app->css('footer.css');
		include $app['view'] . 'footer.php';
		break;
}
	
	
