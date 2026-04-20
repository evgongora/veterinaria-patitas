<?php

declare(strict_types=1);

/**
 * Veterinarios — listado (sesión), ficha staff (?id=), alta/edición solo administrador.
 */
final class VeterinarioController
{
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        if ($method === 'GET') {
            api_require_login();

            $idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;

            if ($idGet > 0) {
                self::fichaStaff($idGet);
            }

            self::listado();

            return;
        }

        if ($method === 'POST') {
            api_require_method('POST');
            AdminController::veterinarioCrear();
        }

        if ($method === 'PUT') {
            api_require_method('PUT');
            AdminController::veterinarioActualizar();
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }

    private static function listado(): void
    {
        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_json(['ok' => true, 'veterinarios' => Veterinario::listAll($pdo)]);
    }

    private static function fichaStaff(int $idGet): void
    {
        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_staff();

        if ($idGet <= 0) {
            api_json(['ok' => false, 'error' => 'Id requerido'], 400);
        }

        $r = Veterinario::findById($pdo, $idGet);
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json($r);
    }
}
