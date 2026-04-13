<?php
/**
 * GET: pagos (filtra por cliente si rol cliente)
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

$sql = <<<'SQL'
SELECT
    p.PAGO_ID_PK,
    p.MONTO_TOTAL,
    p.FECHA_DE_PAGO,
    f.FACTURA_ID_PK,
    mp.METODO,
    ep.DESCRIPCION AS estado_raw,
    uc.EMAIL AS cliente_email
FROM PAGO p
JOIN FACTURA f ON f.FACTURA_ID_PK = p.FACTURA_ID_FK
JOIN CITA c ON c.CITA_ID_PK = f.CITA_ID_FK
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
LEFT JOIN USUARIO uc ON uc.USUARIO_ID_PK = cl.USUARIO_ID_FK
JOIN METODO_PAGO mp ON mp.METODOPAGO_ID_PK = p.METODOPAGO_ID_FK
JOIN ESTADO ep ON ep.ESTADO_ID_PK = p.ESTADO_ID_FK
ORDER BY p.FECHA_DE_PAGO DESC
SQL;

$rows = $pdo->query($sql)->fetchAll();
$sessionEmail = strtolower((string) ($_SESSION['usuario_email'] ?? ''));

function map_estado_pago(string $desc): string
{
    $d = mb_strtolower($desc);
    if (str_contains($d, 'pagad')) {
        return 'Exitoso';
    }
    if (str_contains($d, 'no pag')) {
        return 'Pendiente';
    }

    return $desc;
}

$out = [];
foreach ($rows as $r) {
    $email = strtolower((string) ($r['cliente_email'] ?? ''));
    if (api_is_cliente() && $email !== $sessionEmail) {
        continue;
    }
    $pid = (int) $r['PAGO_ID_PK'];
    $fid = (int) $r['FACTURA_ID_PK'];
    $fecha = (string) $r['FECHA_DE_PAGO'];
    if (strlen($fecha) > 10) {
        $fecha = substr($fecha, 0, 10);
    }
    $out[] = [
        'id' => 'PAG-' . str_pad((string) $pid, 3, '0', STR_PAD_LEFT),
        'facturaId' => 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT),
        'clienteEmail' => (string) ($r['cliente_email'] ?? ''),
        'monto' => (float) $r['MONTO_TOTAL'],
        'fecha' => $fecha,
        'metodo' => (string) $r['METODO'],
        'estado' => map_estado_pago((string) $r['estado_raw']),
    ];
}

api_json(['ok' => true, 'pagos' => $out]);
