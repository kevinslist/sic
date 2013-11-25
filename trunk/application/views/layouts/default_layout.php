<!DOCTYPE html>
<html>
  <head>
    <title>layout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,500,700,900,300,100' rel='stylesheet' type='text/css'>
    <link href="<?= site_url() ?>/assets/kb/lib/bootstrap3/css/bootstrap.css" rel="stylesheet">
    <link href="<?= site_url() ?>/assets/kb/lib/jquery/layout/1.3/jquery.layout.css" rel="stylesheet">
    <link href="<?= site_url() ?>/assets/kb/templates/default/form/css/formalize/kb.bootstrap.formalize.css" rel="stylesheet">
    <link href="<?= site_url() ?>/assets/kb/templates/default/form/css/kb.form-global.css" rel="stylesheet">
    <link href="<?= site_url() ?>/assets/kb/templates/default/form/css/widgets/file-path.css" rel="stylesheet">
    
    <link href="<?= site_url() ?>/assets/css/sic-global.css" rel="stylesheet">
  </head>
  <body data-site-url="<?=site_url()?>">
    <div id="layout-header" class="ui-layout-north"></div>
    <div id="layout-footer" class="ui-layout-south">Loading...</div>
    <div id="navigation-helper" class="ui-layout-east">
      <div id="app-logger"></div>
    </div>
    <div id="application-navigation" class="ui-layout-west">Loading...</div>
    <div id="layout-playlist" class="ui-layout-center">Loading...</div>
    <div id="sic-menu-holder"></div>
    <script src="<?= site_url() ?>/assets/kb/lib/jquery/1.10/js/jquery.js"></script>
    <script src="<?= site_url() ?>/assets/kb/lib/bootstrap3/js/bootstrap.min.js"></script>
    <script src="<?= site_url() ?>/assets/kb/lib/jquery/1.10/js/jquery.ui.js"></script>
    <script src="<?= site_url() ?>/assets/kb/lib/jquery/layout/1.3/jquery.layout.js"></script>
    <script src="<?= site_url() ?>/assets/kb/templates/default/form/js/form.js"></script>
    <script src="<?= site_url() ?>/assets/kb/templates/default/form/js/widgets/file-path.js"></script>
    <script src="<?= site_url() ?>/assets/js/sic-global.js"></script>
  </body>
</html>