<?php

namespace App\Core;

class View
{
    /**
     * Render a view with layout
     * 
     * @param string $viewPath Path to view file relative to app/views/
     * @param array $data Data to pass to the view
     * @param string|null $layout Layout file to use (null for no layout)
     * @return void
     */
    public static function render(string $viewPath, array $data = [], ?string $layout = 'client/layouts/master'): void
    {
        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = dirname(__DIR__) . '/views/' . $viewPath . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("View file not found: $viewFile");
        }
        
        // Get the view content
        $content = ob_get_clean();
        
        // If layout is specified, wrap content in layout
        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/views/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                throw new \Exception("Layout file not found: $layoutFile");
            }
        } else {
            // No layout, just output the content
            echo $content;
        }
    }
}
