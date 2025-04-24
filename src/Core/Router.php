<?php

class Router {
    protected $routes = [];

    public function get($path, $action): void {
        $this->addRoute('GET', $path, $action);
    }

    public function post($path, $action): void {
        $this->addRoute('POST', $path, $action);
    }

    private function addRoute($method, $path, $action): void {
        // Correção do uso do compact
        $this->routes[] = compact('method', 'path', 'action');
    }

    public function dispatch($uri, $method): mixed {
        foreach ($this->routes as $route) {
            if ($route['path'] === $uri && $route['method'] === $method) {
                $action = $route['action'];
    
                // 1) Se for uma Closure (ou qualquer callable), chama direto:
                if (is_callable($action)) {
                    return call_user_func($action);
                }
    
                // 2) Caso contrário, assume que é "Controller@method":
                if (is_string($action) && str_contains($action, '@')) {
                    [$controllerName, $methodName] = explode('@', $action);
                    $controllerClass = "\\Controllers\\$controllerName";
                    $controller = new $controllerClass();
                    return $controller->$methodName();
                }
    
                // 3) Se bater aqui, ação inválida:
                throw new \Exception("Ação de rota inválida para {$route['path']}");
            }
        }
    
        http_response_code(404);
        echo "Rota não encontrada.";
    }
    
}
