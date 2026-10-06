<?php
namespace Simit\Core;

final class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        ob_start(); require BASE_PATH . '/src/Views/' . $view . '.php'; $content = ob_get_clean();
        require BASE_PATH . '/src/Views/layouts/app.php';
    }
}

