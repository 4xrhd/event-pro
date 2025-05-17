<?php
class Router {
    protected $routes = [
        'GET' => [
            'auth/login' => ['AuthController', 'loginView'],
            'auth/register' => ['AuthController', 'registerView'],
            'events/create' => ['EventController', 'createView'],
            'tickets/book' => ['TicketController', 'bookView'],
            'tickets/confirmation' => ['TicketController', 'confirmationView'],
            '' => ['AuthController', 'loginView'],
        ],
        'POST' => [
            'auth/login' => ['AuthController', 'login'],
            'auth/register' => ['AuthController', 'register'],
        ]
    ];

    public function route(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');

        if (isset($this->routes[$requestMethod][$url])) {
            [$controllerName, $method] = $this->routes[$requestMethod][$url];
            $this->callController($controllerName, $method);
            return;
        }

        // Dynamic routing fallback
        $urlParts = explode('/', $url);
        if (count($urlParts) >= 2) {
            $controllerName = ucfirst($urlParts[0]) . 'Controller';
            $method = $urlParts[1] . ($requestMethod === 'POST' ? '' : 'View');
            $params = array_slice($urlParts, 2);
            
            $this->callController($controllerName, $method, $params);
            return;
        }
        
        $this->notFound();
    }

    protected function callController(string $controllerName, string $method, array $params = []): void {
        $controllerFile = "../app/controllers/$controllerName.php";
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }
        
        $this->notFound();
    }

    protected function notFound(): void {
        http_response_code(404);
        $errorView = "../app/views/errors/404.php";
        
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "404 Not Found";
        }
        exit;
    }
}