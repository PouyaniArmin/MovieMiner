<?php

namespace App\Core;

use App\Utility\ErrorHandler;

use function PHPUnit\Framework\exactly;

class View
{
    public string $layout = 'main';
    public function renderViews($view, $params = []): string
    {
        $rv = $this->renderOnlyView($view, $params);
        $rl = $this->renderLayouts();
        return str_replace("{{content}}", $rv, $rl);
    }

    private function renderLayouts(): string
    {
        $layoutPath = __DIR__ . "/../views/layouts/{$this->layout}.php";
        if (!file_exists($layoutPath)) {
            return ErrorHandler::errorMessage("Layout file not found: {$this->layout}.php");
        }
        ob_start();
        include_once $layoutPath;
        return ob_get_clean();
    }
    private function renderOnlyView($view, $params): string
    {
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (!file_exists($viewPath)) {
            return ErrorHandler::errorMessage("View file not found: {$view}.php");
        }
        extract($params);
        ob_start();
        include_once $viewPath;
        return ob_get_clean();
    }
}
