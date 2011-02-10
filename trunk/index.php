<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('includes/app.php');
app::init();
print app::control();
