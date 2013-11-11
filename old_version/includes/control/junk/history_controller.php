<?php
$action = $app->next();

switch($action){
  case('redirect'):
    kb_response::redirect($app['home'].'history/' . $app->next());
    break;
	default:
		include $app['view'] . 'frame.php';
		break;
}
	
	
