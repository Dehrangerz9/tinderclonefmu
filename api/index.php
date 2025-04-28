<?php
phpinfo();
error_reporting(E_ALL);
ini_set('display_errors', 1);

spl_autoload_register(function($class) {
    $path = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

require_once __DIR__ . '/../src/Core/App.php';
require_once __DIR__ . '/../src/Core/Router.php';

$router = new Router();

require_once __DIR__ . '/../src/Routes/web.php';

// Adicionar log para verificar se o código chega aqui
error_log("Router is being called.");

// Certifique-se de que o dispatch está sendo chamado corretamente
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Adicionar log para ver os valores da URI e do método
error_log("Request URI: $uri, Method: $method");

$router->dispatch($uri, $method);
