<?php
/**
 * GET: facturas (?id=FAC-001 para detalle con ítems)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

api_require_method('GET');

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

api_require_login();

$idParam = isset($_GET['id']) ? trim((string) $_GET['id']) : '';

function map_estado_factura(string $desc): string
{
    $d = mb_strtolower($desc);
    if (str_contains($d, 'pagad')) {
        return 'Pagada';
    }
    if (str_contains($d, 'no pag')) {
        return 'Pendiente';
    }
    if (str_contains($d, 'cancel')) {
        return 'Cancelada';
    }

    return $desc;
}

if ($idParam !== '') {
    if (! preg_match('/^FAC-(\d+)$/', $idParam, $m)) {
        api_json(['ok' => false, 'error' => 'Id de factura inválido'], 400);
    }
    $fid = (int) $m[1];

    $sql = <<<'SQL'
SELECT
    f.FACTURA_ID_PK,
    f.FECHA_EMISION,
    f.TOTAL_A_COBRAR,
    ef.DESCRIPCION AS estado_raw,
    a.NOMBRE AS mascota,
    CONCAT(cl.NOMBRE, ' ', cl.APELLIDO_1) AS cliente_nombre,
    uc.EMAIL AS cliente_email
FROM FACTURA f
JOIN CITA c ON c.CITA_ID_PK = f.CITA_ID_FK
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
LEFT JOIN USUARIO uc ON uc.USUARIO_ID_PK = cl.USUARIO_ID_FK
JOIN ESTADO ef ON ef.ESTADO_ID_PK = f.ESTADO_ID_FK
WHERE f.FACTURA_ID_PK = ?
SQL;
    $st = $pdo->prepare($sql);
    $st->execute([$fid]);
    $row = $st->fetch();
    if (! $row) {
        api_json(['ok' => false, 'error' => 'Factura no encontrada'], 404);
    }

    $email = strtolower((string) ($row['cliente_email'] ?? ''));
    if (api_is_cliente()) {
        $sessionEmail = strtolower((string) ($_SESSION['usuario_email'] ?? ''));
        if ($email !== $sessionEmail) {
            api_json(['ok' => false, 'error' => 'No autorizado'], 403);
        }
    }

    $sqlItems = <<<'SQL'
SELECT s.NOMBRE AS servicio, 1 AS cantidad, s.PRECIO AS precio
FROM SERVICIOS_POR_CITA spc
JOIN SERVICIO s ON s.SERVICIO_ID_PK = spc.SERVICIO_ID_FK
WHERE spc.CITA_ID_FK = (SELECT f.CITA_ID_FK FROM FACTURA f WHERE f.FACTURA_ID_PK = ?)
SQL;
    $st = $pdo->prepare($sqlItems);
    $st->execute([$fid]);
    $items = $st->fetchAll();

    if (count($items) === 0) {
        $items = [
            ['servicio' => 'Servicios clínica', 'cantidad' => 1, 'precio' => (float) $row['TOTAL_A_COBRAR']],
        ];
    }

    $fecha = (string) $row['FECHA_EMISION'];
    if (strlen($fecha) > 10) {
        $fecha = substr($fecha, 0, 10);
    }

    api_json([
        'ok' => true,
        'factura' => [
            'id' => 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT),
            'fecha' => $fecha,
            'clienteEmail' => (string) ($row['cliente_email'] ?? ''),
            'clienteNombre' => (string) $row['cliente_nombre'],
            'mascota' => (string) $row['mascota'],
            'estado' => map_estado_factura((string) $row['estado_raw']),
            'total' => (float) $row['TOTAL_A_COBRAR'],
            'items' => array_map(static function ($it) {
                return [
                    'servicio' => (string) $it['servicio'],
                    'cantidad' => (int) $it['cantidad'],
                    'precio' => (float) $it['precio'],
                ];
            }, $items),
        ],
    ]);
}

$sql = <<<'SQL'
SELECT
    f.FACTURA_ID_PK,
    f.FECHA_EMISION,
    f.TOTAL_A_COBRAR,
    ef.DESCRIPCION AS estado_raw,
    a.NOMBRE AS mascota,
    CONCAT(cl.NOMBRE, ' ', cl.APELLIDO_1) AS cliente_nombre,
    uc.EMAIL AS cliente_email
FROM FACTURA f
JOIN CITA c ON c.CITA_ID_PK = f.CITA_ID_FK
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
LEFT JOIN USUARIO uc ON uc.USUARIO_ID_PK = cl.USUARIO_ID_FK
JOIN ESTADO ef ON ef.ESTADO_ID_PK = f.ESTADO_ID_FK
ORDER BY f.FECHA_EMISION DESC
SQL;

$rows = $pdo->query($sql)->fetchAll();
$sessionEmail = strtolower((string) ($_SESSION['usuario_email'] ?? ''));

$out = [];
foreach ($rows as $r) {
    $email = strtolower((string) ($r['cliente_email'] ?? ''));
    if (api_is_cliente() && $email !== $sessionEmail) {
        continue;
    }
    $fid = (int) $r['FACTURA_ID_PK'];
    $fecha = (string) $r['FECHA_EMISION'];
    if (strlen($fecha) > 10) {
        $fecha = substr($fecha, 0, 10);
    }
    $out[] = [
        'id' => 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT),
        'fecha' => $fecha,
        'clienteEmail' => (string) ($r['cliente_email'] ?? ''),
        'clienteNombre' => (string) $r['cliente_nombre'],
        'mascota' => (string) $r['mascota'],
        'estado' => map_estado_factura((string) $r['estado_raw']),
        'total' => (float) $r['TOTAL_A_COBRAR'],
    ];
}

api_json(['ok' => true, 'facturas' => $out]);
