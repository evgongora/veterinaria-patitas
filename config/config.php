<?php
/**
 * Configuración general — Veterinaria Patitas (MVC)
 */

declare(strict_types=1);

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/app');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Administrador (1) o veterinario (2) — para layout sidebar staff vs cliente.
 */
function patitas_es_staff(): bool
{
    $r = isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0;

    return $r === 1 || $r === 2;
}

/** Solo administrador (ROL 1). */
function patitas_es_admin(): bool
{
    return isset($_SESSION['rol_fk']) && (int) $_SESSION['rol_fk'] === 1;
}

function page(string $route, array $query = []): string
{
    $params = array_merge(['r' => $route], $query);
    return 'index.php?' . http_build_query($params);
}

/**
 * Ruta a recurso estático (css, js) bajo app
 */
function asset(string $path): string
{
    $p = ltrim($path, '/');
    if (strpos($p, 'app/') === 0) {
        return $p;
    }
    if (strpos($p, 'css/') === 0 || strpos($p, 'js/') === 0) {
        return 'app/' . $p;
    }
    return $p;
}

/**
 * Fecha calendario "hoy" en zona clínica (Costa Rica), alineada con lo que ve el usuario al reservar.
 */
function patitas_fecha_hoy_ymd(): string
{
    $tz = new DateTimeZone('America/Costa_Rica');

    return (new DateTimeImmutable('now', $tz))->format('Y-m-d');
}
