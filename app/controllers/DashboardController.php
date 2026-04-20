<?php

declare(strict_types=1);

/**
 * Panel — métricas cliente o staff.
 */
final class DashboardController
{
    public static function index(): void
    {
        api_require_method('GET');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_login();

        if (api_is_cliente()) {
            $cid = api_cliente_id($pdo);
            api_json(Dashboard::datosCliente($pdo, $cid));
        }

        api_json(Dashboard::datosStaff($pdo));
    }
}
