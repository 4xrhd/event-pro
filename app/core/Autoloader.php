<?php
class Autoloader {
    private $baseDir;

    public function __construct($baseDir = null) {
        $this->baseDir = $baseDir ?: __DIR__ . '/../..';
    }

    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }

    protected function loadClass($className) {
        $file = $this->baseDir . '/' . str_replace('\\', '/', $className) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        return false;
    }
}