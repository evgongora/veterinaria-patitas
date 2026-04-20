<?php

declare(strict_types=1);

/**
 * Evaluaciones — públicas, cliente, staff y mutaciones cliente.
 */
final class EvaluacionController
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
            $limitPublic = isset($_GET['limitPublic']) ? (int) $_GET['limitPublic'] : 25;
            if ($limitPublic < 1) {
                $limitPublic = 30;
            }
            if ($limitPublic > 100) {
                $limitPublic = 100;
            }

            if (api_session_user_id() !== null && api_is_staff()) {
                ClinicaController::evaluacionesStaff($pdo);

                return;
            }

            if (api_session_user_id() !== null && api_is_cliente()) {
                $uid = api_require_login();
                try {
                    $publicas = Evaluacion::listarPublicas($pdo, $limitPublic);
                    $mias = Evaluacion::listarPorUsuario($pdo, $uid);
                    api_json(['ok' => true, 'vista' => 'cliente', 'publicas' => $publicas, 'mias' => $mias]);
                } catch (Throwable $e) {
                    api_json([
                        'ok' => false,
                        'error' => 'No se pudieron cargar las evaluaciones.',
                        'publicas' => [],
                        'mias' => [],
                    ], 503);
                }
            }

            try {
                $publicas = Evaluacion::listarPublicas($pdo, $limitPublic);
                api_json(['ok' => true, 'vista' => 'anon', 'publicas' => $publicas]);
            } catch (Throwable $e) {
                api_json([
                    'ok' => false,
                    'error' => 'Tabla EVALUACION no disponible. Ejecuta sql/schema.sql o sql/migration_evaluaciones.sql.',
                    'publicas' => [],
                ], 503);
            }
        }

        if ($method === 'POST') {
            api_require_cliente();
            $uid = (int) api_require_login();
            $r = Evaluacion::crear($pdo, $uid, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true, 'id' => $r['id']]);
        }

        if ($method === 'PUT') {
            api_require_cliente();
            $uid = (int) api_require_login();
            $r = Evaluacion::actualizar($pdo, $uid, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        if ($method === 'DELETE') {
            api_require_cliente();
            $uid = (int) api_require_login();
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $r = Evaluacion::eliminar($pdo, $uid, $id);
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }
}
