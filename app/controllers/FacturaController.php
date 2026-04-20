<?php

declare(strict_types=1);

/**
 * Facturas.
 */
final class FacturaController
{
    public static function handle(): void
    {
        api_require_method('GET');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_login();

        $idParam = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
        $sessionEmail = (string) ($_SESSION['usuario_email'] ?? '');

        if ($idParam !== '') {
            if (! preg_match('/^FAC-(\d+)$/', $idParam, $m)) {
                api_json(['ok' => false, 'error' => 'Id de factura inválido'], 400);
            }
            $fid = (int) $m[1];
            $r = Factura::detalle($pdo, $fid, api_is_cliente(), $sessionEmail);
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json($r);
        }

        api_json(Factura::listar($pdo, api_is_cliente(), $sessionEmail));
    }
}
