<?php

declare(strict_types=1);

/**
 * Citas — listado, ocupadas, pendientes staff, detalle cliente, alta, edición, aceptar (staff).
 */
final class CitaController
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

            if (isset($_GET['pendientes']) && $_GET['pendientes'] === '1') {
                api_require_staff();
                if (api_rol_fk() === 1) {
                    api_json(['ok' => true, 'citas' => Cita::listarPendientesAprobacion($pdo, null)]);
                }
                $vid = api_veterinario_id($pdo);
                if ($vid === null) {
                    api_json(['ok' => true, 'citas' => []]);
                }
                api_json(['ok' => true, 'citas' => Cita::listarPendientesAprobacion($pdo, $vid)]);
            }

            if (isset($_GET['ocupadas']) && $_GET['ocupadas'] === '1') {
                $fecha = isset($_GET['fecha']) ? trim((string) $_GET['fecha']) : '';
                $vetId = isset($_GET['veterinarioId']) ? (int) $_GET['veterinarioId'] : 0;
                $except = isset($_GET['exceptCitaId']) ? (int) $_GET['exceptCitaId'] : 0;
                if ($fecha === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) || $vetId <= 0) {
                    api_json(['ok' => false, 'error' => 'Indica fecha (YYYY-MM-DD) y veterinarioId'], 400);
                }
                $exc = $except > 0 ? $except : null;
                api_json(['ok' => true, 'horasOcupadas' => Cita::horasOcupadasParaReserva($pdo, $vetId, $fecha, $exc)]);
            }

            if (isset($_GET['soloHoy']) && $_GET['soloHoy'] === '1') {
                $cid = api_is_cliente() ? api_cliente_id($pdo) : null;
                api_json([
                    'ok' => true,
                    'citas' => Cita::listarCitasHoy($pdo, api_is_cliente(), $cid),
                    'fechaReferencia' => patitas_fecha_hoy_ymd(),
                ]);
            }

            $citaId = isset($_GET['citaId']) ? (int) $_GET['citaId'] : 0;
            if ($citaId > 0) {
                if (! api_is_cliente()) {
                    api_json(['ok' => false, 'error' => 'Solo clientes'], 403);
                }
                $cid = api_cliente_id($pdo);
                if ($cid === null) {
                    api_json(['ok' => false, 'error' => 'Perfil de cliente no encontrado'], 400);
                }
                api_json(Cita::obtenerParaCliente($pdo, $citaId, $cid));
            }

            $cid = api_is_cliente() ? api_cliente_id($pdo) : null;
            $citas = Cita::listarParaUsuario($pdo, api_is_cliente(), $cid);
            api_json(['ok' => true, 'citas' => $citas]);
        }

        if ($method === 'PUT') {
            api_require_login();
            api_require_cliente();
            $cid = api_cliente_id($pdo);
            if ($cid === null) {
                api_json(['ok' => false, 'error' => 'Perfil de cliente no encontrado'], 400);
            }
            $r = Cita::actualizarCliente($pdo, $cid, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        if ($method === 'POST') {
            api_require_login();
            $body = api_json_body();
            if (($body['accion'] ?? '') === 'aceptar') {
                api_require_staff();
                $citaId = (int) ($body['citaId'] ?? 0);
                $r = Cita::aceptarPorStaff(
                    $pdo,
                    $citaId,
                    api_rol_fk() === 1,
                    api_rol_fk() === 2 ? api_veterinario_id($pdo) : null
                );
                if (! $r['ok']) {
                    api_json(['ok' => false, 'error' => $r['error']], $r['code']);
                }
                api_json(['ok' => true]);
            }

            api_require_cliente();

            $cid = api_cliente_id($pdo);
            if ($cid === null) {
                api_json(['ok' => false, 'error' => 'Perfil de cliente no encontrado'], 400);
            }

            $r = Cita::crearCliente($pdo, $cid, $body);
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }

            api_json(['ok' => true, 'citaId' => $r['citaId']]);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }
}
