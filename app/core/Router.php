<?php
// app/core/Router.php

class Router {
    protected $routes = [
        // Auth routes
        'auth/login'      => ['AuthController', 'login'],
        'auth/register'   => ['AuthController', 'register'],
        'auth/logout'     => ['AuthController', 'logout'],

        // Event routes
        'events'          => ['EventController', 'index'],
        'events/create'   => ['EventController', 'create'],
        'events/show'     => ['EventController', 'show'],
        'events/edit'    => ['EventController', 'edit'],
        'events/delete'   => ['EventController', 'delete'],

        // Ticket routes
        'tickets'         => ['TicketController', 'index'],
        'tickets/book'    => ['TicketController', 'book'],
        'tickets/view'   => ['TicketController', 'view'],

        // Default route
        ''                => ['AuthController', 'login'],
    ];

    public function __construct() {
        $this->initialize();
    }

    protected function initialize() {
        // You could add route registration methods here if needed
    }

    public function route() {
        $url = $this->getUrl();
        
        // Check defined routes first
        if (isset($this->routes[$url])) {
            $this->dispatch($this->routes[$url]);
            return;
        }

        // Fallback to dynamic routing
        $this->dynamicRoute($url);
    }

    protected function getUrl() {
        $url = $_GET['url'] ?? '';
        return rtrim($url, '/');
    }

    protected function dispatch($routeInfo, $params = []) {
        [$controllerName, $method] = $routeInfo;
        $controllerPath = "../app/controllers/$controllerName.php";
        
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName;
                
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }
        
        $this->notFound();
    }

    protected function dynamicRoute($url) {
        $urlParts = explode('/', $url);
        
        if (count($urlParts) >= 2) {
            $controllerName = ucfirst($urlParts[0]) . 'Controller';
            $method = $urlParts[1];
            $params = array_slice($urlParts, 2);
            
            $this->dispatch([$controllerName, $method], $params);
            return;
        }
        
        $this->notFound();
    }

    protected function notFound() {
        http_response_code(404);
        
        // You could load a custom 404 view here
        $viewPath = "../app/views/errors/404.php";
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "404 Not Found";
        }
        
        exit;
    }
}