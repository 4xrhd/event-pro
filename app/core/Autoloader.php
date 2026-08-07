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
        $normalized = str_replace('\\', '/', $className);
        $searchPaths = [
            $this->baseDir . '/' . $normalized . '.php',
            $this->baseDir . '/app/core/' . $normalized . '.php',
            $this->baseDir . '/app/controllers/' . $normalized . '.php',
            $this->baseDir . '/app/models/' . $normalized . '.php',
        ];

        foreach ($searchPaths as $file) {
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        return false;
    }
}