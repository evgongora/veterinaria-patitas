<?php

declare(strict_types=1);

/**
 * Controlador administrador — operaciones exclusivas del rol 1 (p. ej. alta/edición de veterinarios).
 */
final class AdminController
{
    public static function veterinarioCrear(): void
    {
        api_require_login();
        api_require_admin();

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        $r = Veterinario::create($pdo, api_json_body());
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json(['ok' => true, 'veterinarioId' => $r['veterinarioId']]);
    }

    public static function veterinarioActualizar(): void
    {
        api_require_login();
        api_require_admin();

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        $r = Veterinario::update($pdo, api_json_body());
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json(['ok' => true]);
    }
}
