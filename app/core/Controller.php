<?php
class Controller {
    protected function view($viewPath, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Build full path to view
        $viewFile = __DIR__ . "/../views/$viewPath.php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View file not found: $viewPath");
        }
    }
    



    protected function model($model) {
        $modelFile = __DIR__ . "/../models/$model.php";
        if (!file_exists($modelFile)) {
            throw new Exception("Model file not found: $model");
        }
        require_once $modelFile;
        return new $model();
    }
    protected function notFound() {
        http_response_code(404);
        $errorView = __DIR__ . "/../views/errors/404.php";
        
        if (file_exists($errorView)) {
            require_once $errorView;
        } else {
            echo "404 Not Found";
        }
        exit;
    }
}
