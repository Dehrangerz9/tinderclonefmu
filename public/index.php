<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../src/Core/App.php';

use App\Core\App;

$app = new App();
$app->run();
