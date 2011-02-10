<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>sic</title>
  <link rel="stylesheet" type="text/css" href="<?=APP_HOME .'css/reset.css';?>" />
  <link rel="stylesheet" type="text/css" href="<?=APP_HOME_LAYOUT .'css/ui/jquery.ui.css';?>" />
  <?php controller::css(LAYOUT_CSS .'app.css');?>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.ui.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.cookie.js"></script>
  <script type="text/javascript" src="<?=APP_HOME_JS?>sub-app.js"></script>
  <script type="text/javascript" src="<?=APP_HOME_JS?>collection_scanner/collection_scanner_menu.js"></script>
</head>
<body id="page-body-menu" class="collection-scanner-menu">
  Last Run: <?=$lastrun?><br/>
  Running: <?=$collection_scanner_daemon_running?><br/>
  <input type="button" value="start" class="start-collection-scanner-button" />
</body>
</html>