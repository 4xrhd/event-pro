<?php
class Controller {
    protected function view($viewPath, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Build full path to view
        $viewFile = "../app/views/$viewPath.php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            throw new Exception("View file not found: $viewPath");
        }
    }
    



    protected function model($model) {
        require_once "../app/models/$model.php";
        return new $model();
    }
    protected function notFound() {
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
