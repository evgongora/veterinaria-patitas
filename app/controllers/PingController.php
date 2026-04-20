<?php

declare(strict_types=1);

/**
 * Diagnóstico — conexión a base de datos.
 */
final class PingController
{
    public static function handle(): void
    {
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
    }
}
