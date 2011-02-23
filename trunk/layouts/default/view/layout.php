<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>sic</title>
  <link rel="stylesheet" type="text/css" href="<?=APP_HOME .'css/reset.css';?>" />
  <link rel="stylesheet" type="text/css" href="<?=APP_HOME_LAYOUT .'css/ui/jquery.ui.css';?>" />
  <link rel="stylesheet" type="text/css" href="<?=APP_HOME_LAYOUT .'css/layout.css';?>" />
  <?php controller::css(LAYOUT_CSS .'app.css');?>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.ui.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.cookie.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.websocket.js"></script>
  <script type="text/javascript" src="<?=APP_HOME?>js/jquery/jquery.layout.js"></script>
  <script type="text/javascript" src="<?=APP_HOME_LAYOUT?>js/layout.js"></script>
  <script type="text/javascript" src="<?=APP_HOME_JS?>app.js"></script>
</head>
<body id="page-body">
  <div id="layout-header" class="ui-layout-north">Loading...</div>
  <div id="layout-footer" class="ui-layout-south">Loading...</div>
  <div id="navigation-helper" class="ui-layout-east">
    <div id="app-logger"></div>
  </div>
  <div id="application-navigation" class="ui-layout-west">Loading...</div>
  <div id="layout-playlist" class="ui-layout-center">Loading...</div>
</body>
</html>