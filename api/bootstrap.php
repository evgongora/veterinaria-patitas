<?php
/**
 * Inicialización común para endpoints API (JSON + sesión)
 */

declare(strict_types=1);

if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/app');
}

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

/** Evita caché de respuestas API en desarrollo */
header('Cache-Control: no-store, no-cache, must-revalidate');

/**
 * @param array<string, mixed> $data
 */
function api_json(array $data, int $httpCode = 200): void
{
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function api_require_method(string ...$methods): void
{
    $m = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if (! in_array($m, $methods, true)) {
        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }
}

/**
 * @return array<string, mixed>
 */
function api_json_body(): array
{
    $raw = file_get_contents('php://input') ?: '';
    if ($raw === '') {
        return [];
    }
    try {
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : [];
    } catch (Throwable $e) {
        api_json(['ok' => false, 'error' => 'JSON inválido'], 400);
    }
}

function api_session_user_id(): ?int
{
    if (empty($_SESSION['usuario_id'])) {
        return null;
    }

    return (int) $_SESSION['usuario_id'];
}

function api_require_login(): int
{
    $id = api_session_user_id();
    if ($id === null || $id <= 0) {
        api_json(['ok' => false, 'error' => 'No autenticado'], 401);
    }

    return $id;
}

function api_rol_fk(): int
{
    return isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0;
}

function api_is_cliente(): bool
{
    return api_rol_fk() === 3;
}

function api_is_staff(): bool
{
    $r = api_rol_fk();

    return $r === 1 || $r === 2;
}

function api_require_staff(): void
{
    if (! api_is_staff()) {
        api_json(['ok' => false, 'error' => 'Sin permiso'], 403);
    }
}

/** Solo administrador (ROL 1). Veterinarios (2) no pasan. */
function api_require_admin(): void
{
    if (api_rol_fk() !== 1) {
        api_json(['ok' => false, 'error' => 'Requiere permisos de administrador'], 403);
    }
}

function api_require_cliente(): void
{
    if (! api_is_cliente()) {
        api_json(['ok' => false, 'error' => 'Solo clientes'], 403);
    }
}

function api_cliente_id(PDO $pdo): ?int
{
    $uid = api_session_user_id();
    if ($uid === null) {
        return null;
    }
    $st = $pdo->prepare('SELECT CLIENTE_ID_PK FROM CLIENTE WHERE USUARIO_ID_FK = ? LIMIT 1');
    $st->execute([$uid]);
    $v = $st->fetchColumn();

    return $v !== false ? (int) $v : null;
}
