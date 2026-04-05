<?php
/**
 * Configuración general — Veterinaria Patitas (MVC)
 */

declare(strict_types=1);

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

function page(string $route, array $query = []): string
{
    $params = array_merge(['r' => $route], $query);
    return 'index.php?' . http_build_query($params);
}

/**
 * Ruta a recurso estático (css, js, imágenes) desde la raíz del proyecto.
 */
function asset(string $path): string
{
    return ltrim($path, '/');
}
