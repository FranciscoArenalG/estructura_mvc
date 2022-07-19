<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('America/Mexico_City');
require_once("libs/database.php");
require_once("libs/menu.php");
require_once("libs/controller.base.php");
require_once("libs/view.base.php");
require_once("libs/model.base.php");
require_once("libs/app.php");

require_once("config/config.php");

$app = new App();
 ?>
