<?php

namespace Source\Core;

use Source\Exceptions\ViewFileNotFound;

class View
{
    /**
     * Renders a PHP file
     * @throws ViewFileNotFound
     */
    public function render(string $template, array $context = [])
    {
        extract($context, EXTR_SKIP);
        $file = VIEWS_PATH . "/$template" . (!str_contains($template, '.php') ? '.php' : '');
        $file = fix_dir_sep($file);
        if (file_exists($file) && is_readable($file)) {
            require $file;
        } else {
            throw new ViewFileNotFound("Template \"$template\" not found in " . dirname($file));
        }
    }
}