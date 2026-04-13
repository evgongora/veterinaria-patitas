<?php
/**
 * GET: comprobar conexión a la base de datos (diagnóstico)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

api_require_method('GET');

try {
    $pdo = Database::getConnection();
    $pdo->query('SELECT 1');
    api_json(['ok' => true, 'db' => 'conectado']);
} catch (Throwable $e) {
    api_json([
        'ok' => false,
        'db' => 'error',
        'mensaje' => getenv('APP_DEBUG') ? $e->getMessage() : 'Error de conexión',
    ], 503);
}
