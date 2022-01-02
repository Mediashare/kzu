<?php
require '../vendor/autoload.php';

error_reporting(E_ALL ^ E_DEPRECATED);

$app = new Kzu\App();
$app->run();
