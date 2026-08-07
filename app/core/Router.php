<?php
class Router {
    
    protected $routes = [
        'GET' => [
            '' => ['AuthController', 'loginView'],
            'auth/login' => ['AuthController', 'loginView'],
            'auth/register' => ['AuthController', 'registerView'],
            'auth/logout' => ['AuthController', 'logout'],
            'events' => ['EventController', 'indexView'],
            'events/index' => ['EventController', 'indexView'],
            'events/create' => ['EventController', 'createView'],
            'events/view' => ['EventController', 'indexView'],
            'events/view/:id' => ['EventController', 'viewEvent'],
            'events/edit/:id' => ['EventController', 'editView'],
            'tickets/book/:id' => ['TicketController', 'bookView'],
            'tickets/confirm/:id' => ['TicketController', 'confirmView'],
            'tickets/download/:id' => ['TicketController', 'download'],
            'tickets/my-tickets' => ['TicketController', 'myTicketsView'],
            'tickets/checkin' => ['TicketController', 'checkinView'],
        ],
        'POST' => [
            'auth/login' => ['AuthController', 'login'],
            'auth/register' => ['AuthController', 'register'],
            'events/create' => ['EventController', 'create'],
            'events/edit/:id' => ['EventController', 'edit'],
            'events/delete/:id' => ['EventController', 'delete'],
            'tickets/book/:id' => ['TicketController', 'book'],
            'tickets/checkin' => ['TicketController', 'checkin'],
        ]
    ];

    public function __construct() {
        require_once __DIR__ . '/Controller.php';
    }

    public function route(): void {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (!isset($this->routes[$requestMethod])) {
            $this->notFound();
        }

        // Extract URL string from $_GET['url'] or $_SERVER['REQUEST_URI']
        $rawUrl = $_GET['url'] ?? '';
        if ($rawUrl === '') {
            $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
            $path = parse_url($requestUri, PHP_URL_PATH) ?? '/';
            
            // Remove script directory prefix if hosted in subfolder
            $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
            if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($path, $scriptDir) === 0) {
                $path = substr($path, strlen($scriptDir));
            }

            // Remove /index.php prefix if present
            $path = preg_replace('/^\/index\.php/', '', $path);
            $rawUrl = $path;
        }

        $url = trim($this->sanitizeUrl($rawUrl), '/');
    
        // 1. Check exact matches
        if (isset($this->routes[$requestMethod][$url])) {
            [$controllerName, $method] = $this->routes[$requestMethod][$url];
            $this->callController($controllerName, $method);
            return;
        }
    
        // 2. Check parameterized routes (e.g. events/view/:id)
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
    
        // 3. Fallback dynamic routing
        $urlParts = explode('/', $url);
        if (count($urlParts) >= 2) {
            $controllerName = ucfirst($urlParts[0]) . 'Controller';
            $method = $urlParts[1] . ($requestMethod === 'POST' ? '' : 'View');
            $params = array_slice($urlParts, 2);
            
            $this->callController($controllerName, $method, $params);
            return;
        }

        // 4. Default home fallback for empty URL
        if ($url === '') {
            $this->callController('AuthController', 'loginView');
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
            echo "<h1>404 Not Found</h1><p>The requested page could not be found.</p>";
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
