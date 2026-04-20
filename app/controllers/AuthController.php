<?php

declare(strict_types=1);

/**
 * Autenticación — sesión, login, registro público, logout.
 */
final class AuthController
{
    public static function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        if ($method === 'GET') {
            $u = User::sessionPayloadFromGlobals();
            if ($u !== null) {
                api_json(['ok' => true, 'usuario' => $u]);
            }
            api_json(['ok' => false, 'usuario' => null]);
        }

        api_require_method('POST');
        $body = api_json_body();
        $email = isset($body['email']) ? trim((string) $body['email']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json([
                'ok' => false,
                'error' => 'No se pudo conectar a la base de datos',
                'detalle' => getenv('APP_DEBUG') ? $e->getMessage() : null,
            ], 503);
        }

        $r = User::login($pdo, $email, $password);
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json(['ok' => true, 'usuario' => $r['usuario']]);
    }

    public static function registro(): void
    {
        api_require_method('POST');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        $r = User::registrarCliente($pdo, api_json_body());
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json(['ok' => true, 'mensaje' => $r['mensaje']]);
    }

    public static function logout(): void
    {
        api_require_method('POST');
        User::logout();
        api_json(['ok' => true]);
    }
}
