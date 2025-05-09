<?php
class Router {
    public function route() {
        $url = $_GET['url'] ?? 'auth/login';
        $url = explode('/', rtrim($url, '/'));
        $controllerName = ucfirst($url[0]) . 'Controller';
        $method = $url[1] ?? 'index';
        $controllerFile = "../app/controllers/$controllerName.php";
        if (file_exists($controllerFile)) {
            require $controllerFile;
            $controller = new $controllerName;
            if (method_exists($controller, $method)) {
                $controller->$method();
                return;
            }
        }
        echo "404 Not Found";
    }
}