<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function($class) {
    // Transforma namespace em caminho de arquivo
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});


require_once __DIR__ . '/../src/Core/App.php';
require_once __DIR__ . '/../src/Core/Router.php';

$router = new Router();

require_once __DIR__ . '/../src/Routes/web.php';

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);

//use App\Core\App;
//$app = new App();
//$app->run();
