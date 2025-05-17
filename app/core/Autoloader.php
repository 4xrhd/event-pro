<?php
class Autoloader {
    public function register() {
        spl_autoload_register([$this, 'loadClass']);
    }

    protected function loadClass($className) {
        $file = __DIR__.'/../../'.str_replace('\\', '/', $className).'.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
