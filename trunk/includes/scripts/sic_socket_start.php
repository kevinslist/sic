#!/usr/bin/php -q
<?php
chdir(dirname(__file__));
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../app.php');
app::init();
sic_socket::create($argv);
