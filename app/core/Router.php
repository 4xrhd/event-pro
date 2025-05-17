<?php
class Router {
    
    protected $routes = [
        'GET' => [
            'auth/login' => ['AuthController', 'loginView'],
            'auth/register' => ['AuthController', 'registerView'],
            'events' => ['EventController', 'indexView'],        // Main events listing
            'events/index' => ['EventController', 'indexView'],  // Alternative
            'events/create' => ['EventController', 'createView'],
            'events/view/:id' => ['EventController', 'viewEvent'],
            '' => ['AuthController', 'loginView'],               // Default route
        ],
        'POST' => [
            'auth/login' => ['AuthController', 'login'],
            'auth/register' => ['AuthController', 'register'],
            'events/create' => ['EventController', 'create'],
        ]
    ];

    public function __construct() {
        // Load base Controller
        require_once 'Controller.php';
    }

    public function route(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $url = $this->sanitizeUrl($_GET['url'] ?? '');
        $url = rtrim($url, '/');
    
        // 1. First check exact matches
        if (isset($this->routes[$requestMethod][$url])) {
            [$controllerName, $method] = $this->routes[$requestMethod][$url];
            $this->callController($controllerName, $method);
            return;
        }
    
        // 2. Check parameterized routes
        foreach ($this->routes[$requestMethod] as $route => $handler) {
            if (strpos($route, ':') !== false) {
                $pattern = preg_replace('/:[^\/]+/', '([^\/]+)', $route);
                $pattern = str_replace('/', '\/', $pattern);
                if (preg_match("@^{$pattern}$@", $url, $matches)) {
                    [$controllerName, $method] = $handler;
                    $params = array_slice($matches, 1);
                    $this->callController($controllerName, $method, $params);
                    return;
                }
            }
        }
    
        // 3. Fallback to dynamic routing if enabled
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
        $controllerFile = __DIR__ . "/../controllers/$controllerName.php";
        
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $method)) {
                    // Sanitize parameters before passing to controller
                    $sanitizedParams = array_map([$this, 'sanitizeInput'], $params);
                    call_user_func_array([$controller, $method], $sanitizedParams);
                    return;
                }
            }
        }
        
        $this->notFound();
    }

    protected function notFound(): void {
        http_response_code(404);
        $errorView = __DIR__ . "/../views/errors/404.php";
        
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "404 Not Found";
        }
        exit;
    }

    protected function sanitizeUrl(string $url): string {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    protected function sanitizeInput($input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}