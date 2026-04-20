<?php

declare(strict_types=1);

/**
 * Pagos — listado (cliente: propios; staff: todos), citas cobrables y registro (staff).
 */
final class PagoController
{
    public static function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        if ($method === 'GET') {
            api_require_login();

            if (isset($_GET['citasCobro']) && $_GET['citasCobro'] === '1') {
                api_require_staff();
                $esAdmin = api_rol_fk() === 1;
                $vid = api_rol_fk() === 2 ? api_veterinario_id($pdo) : null;
                if (! $esAdmin && ($vid === null || $vid <= 0)) {
                    api_json([
                        'ok' => true,
                        'citas' => [],
                        'metodos' => Pago::listarMetodosPago($pdo),
                    ]);
                }
                api_json([
                    'ok' => true,
                    'citas' => Cita::listarParaCobro($pdo, $esAdmin, $vid),
                    'metodos' => Pago::listarMetodosPago($pdo),
                ]);
            }

            $sessionEmail = (string) ($_SESSION['usuario_email'] ?? '');
            api_json(Pago::listar($pdo, api_is_cliente(), $sessionEmail));
        }

        if ($method === 'POST') {
            api_require_login();
            api_require_staff();
            $r = Pago::registrarDesdeCita(
                $pdo,
                api_json_body(),
                api_rol_fk() === 1,
                api_rol_fk() === 2 ? api_veterinario_id($pdo) : null
            );
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json($r);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }
}
