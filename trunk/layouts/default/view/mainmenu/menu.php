<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>sic</title>
    <link rel="stylesheet" type="text/css" href="<?= APP_HOME . 'css/reset.css'; ?>" />
    <link rel="stylesheet" type="text/css" href="<?= APP_HOME_LAYOUT . 'css/ui/jquery.ui.css'; ?>" />
    <?php controller::css(LAYOUT_CSS . 'app.css'); ?>
    <link rel="stylesheet" type="text/css" href="<?= APP_HOME_LAYOUT . 'css/menu.css'; ?>" />
    <script type="text/javascript" src="<?= APP_HOME ?>js/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?= APP_HOME ?>js/jquery/jquery.ui.js"></script>
    <script type="text/javascript" src="<?= APP_HOME ?>js/jquery/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?= APP_HOME_LAYOUT ?>js/menu.js"></script>
  </head>
  <body id="menu-body">
    <div id="menu-tabs" class="menu-wrapper">
      <div class="ui-tabs-nav-wrapper">
        <ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
          <li><a href="#tabs-1">Favorites</a></li>
          <li><a href="#tabs-2">Colors</a></li>
          <li>-- Divider --</li>
          <li><a href="#collection-scan-tab">Collection Scanner</a></li>
        </ul>
      </div>
      <div id="tabs-1">
        <h2>Content heading 1</h2>
        <p>Proin elit arcu, rutrum commodo, vehicula tempus, commodo a, risus. Curabitur nec arcu. Donec sollicitudin mi sit amet mauris. Nam elementum quam ullamcorper ante. Etiam aliquet massa et lorem. Mauris dapibus lacus auctor risus. Aenean tempor ullamcorper leo. Vivamus sed magna quis ligula eleifend adipiscing. Duis orci. Aliquam sodales tortor vitae ipsum. Aliquam nulla. Duis aliquam molestie erat. Ut et mauris vel pede varius sollicitudin. Sed ut dolor nec orci tincidunt interdum. Phasellus ipsum. Nunc tristique tempus lectus.</p>
      </div>
      <div id="tabs-2">
        <h2>Content heading 2</h2>
        <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
      </div>
      <div id="collection-scan-tab">
        <h2>Collection Scanner</h2>
        <div>
            collection scanner collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner
            collection scanner collection scanner collection scanner collection scanner
        </div>
      </div>
    </div>
  </body>
</html>