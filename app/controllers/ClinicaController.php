<?php

declare(strict_types=1);

/**
 * Controlador de operación clínica (equivalente a "Taller" en el ejemplo de la profesora):
 * clientes (staff), inventario, servicios, catálogos, evaluaciones (vista staff).
 */
final class ClinicaController
{
    public static function evaluacionesStaff(PDO $pdo): void
    {
        $limitStaff = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
        api_require_staff();
        try {
            $rows = Evaluacion::listarStaff($pdo, $limitStaff);
            api_json(['ok' => true, 'vista' => 'staff', 'evaluaciones' => $rows]);
        } catch (Throwable $e) {
            api_json([
                'ok' => false,
                'error' => 'No se pudieron cargar las evaluaciones.',
                'evaluaciones' => [],
            ], 503);
        }
    }

    public static function clientes(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        if ($method === 'GET') {
            api_require_login();
            api_require_staff();

            $idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;

            if ($idGet > 0) {
                $r = Cliente::findForStaff($pdo, $idGet);
                if (! $r['ok']) {
                    api_json(['ok' => false, 'error' => $r['error']], $r['code']);
                }
                api_json($r);
            }

            api_json(['ok' => true, 'clientes' => Cliente::listForStaff($pdo)]);
        }

        if ($method === 'POST') {
            api_require_login();
            api_require_staff();
            $r = Cliente::createByStaff($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true, 'clienteId' => $r['clienteId']]);
        }

        if ($method === 'PUT') {
            api_require_login();
            api_require_staff();
            $r = Cliente::updateByStaff($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }

    public static function inventario(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        if ($method === 'GET') {
            api_require_login();
            api_require_staff();

            if (isset($_GET['tipos']) && (string) $_GET['tipos'] === '1') {
                api_json(['ok' => true, 'tipos' => Medicamento::tiposActivos($pdo)]);
            }

            $idUno = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            if ($idUno > 0) {
                $r = Medicamento::obtener($pdo, $idUno);
                if (! $r['ok']) {
                    api_json(['ok' => false, 'error' => $r['error']], $r['code']);
                }
                api_json($r);
            }

            $r = Medicamento::listar($pdo);
            api_json($r);
        }

        if ($method === 'POST') {
            api_require_login();
            api_require_staff();
            $r = Medicamento::crear($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true, 'idNum' => $r['idNum']]);
        }

        if ($method === 'PUT') {
            api_require_login();
            api_require_staff();
            $r = Medicamento::actualizar($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        if ($method === 'DELETE') {
            api_require_login();
            api_require_staff();
            $idNum = isset($_GET['idNum']) ? (int) $_GET['idNum'] : 0;
            $r = Medicamento::bajaLogica($pdo, $idNum);
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }

    public static function servicios(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos', 'servicios' => []], 503);
        }

        if ($method === 'GET') {
            api_json(['ok' => true, 'servicios' => Servicio::listarTodos($pdo)]);
        }

        api_require_login();

        if (! api_is_staff()) {
            api_json(['ok' => false, 'error' => 'Sin permiso'], 403);
        }

        if ($method === 'POST') {
            $r = Servicio::crear($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true, 'servicio' => $r['servicio']]);
        }

        if ($method === 'PUT') {
            $r = Servicio::actualizar($pdo, api_json_body());
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true, 'servicio' => $r['servicio']]);
        }

        if ($method === 'DELETE') {
            $idRaw = $_GET['id'] ?? '';
            $r = Servicio::desactivar($pdo, (string) $idRaw);
            if (! $r['ok']) {
                api_json(['ok' => false, 'error' => $r['error']], $r['code']);
            }
            api_json(['ok' => true]);
        }

        api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
    }

    public static function razas(): void
    {
        api_require_method('GET');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_login();
        api_json(Catalogo::razas($pdo));
    }

    public static function tiposCita(): void
    {
        api_require_method('GET');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_login();
        api_json(Catalogo::tiposCita($pdo));
    }
}
