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
}