<?php

namespace App\Core;

class View
{
    public static function render(string $viewPath, array $data = [], ?string $layout = 'client/layouts/master'): void
    {
        extract($data);
        
        ob_start();
        
        $viewFile = dirname(__DIR__) . '/views/' . $viewPath . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }
        
        $content = ob_get_clean();
        
        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                throw new \Exception("Layout file not found: $layoutFile");
            }
        } else {
            echo $content;
        }
    }
}
