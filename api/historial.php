<?php
/**
 * GET: historial clínico por animal (?animalId=1)
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

$animalId = isset($_GET['animalId']) ? (int) $_GET['animalId'] : 0;
if ($animalId <= 0) {
    api_json(['ok' => false, 'error' => 'animalId requerido'], 400);
}

$st = $pdo->prepare(
    'SELECT a.CLIENTE_ID_FK FROM ANIMAL a WHERE a.ANIMAL_ID_PK = ?'
);
$st->execute([$animalId]);
$owner = $st->fetchColumn();
if ($owner === false) {
    api_json(['ok' => false, 'error' => 'Animal no encontrado'], 404);
}

if (api_is_cliente()) {
    $cid = api_cliente_id($pdo);
    if ($cid === null || (int) $owner !== $cid) {
        api_json(['ok' => false, 'error' => 'No autorizado'], 403);
    }
}

$sql = <<<'SQL'
SELECT
    c.FECHA,
    c.NOTAS_VETERINARIO,
    t.DESCRIPCION AS tipo,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario
FROM CITA c
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
WHERE c.ANIMAL_ID_FK = ?
ORDER BY c.FECHA DESC
SQL;
$st = $pdo->prepare($sql);
$st->execute([$animalId]);
$rows = $st->fetchAll();

$out = [];
foreach ($rows as $r) {
    $fecha = (string) $r['FECHA'];
    $notas = (string) ($r['NOTAS_VETERINARIO'] ?? '');
    $out[] = [
        'fecha' => $fecha,
        'diagnostico' => (string) $r['tipo'],
        'tratamiento' => $notas !== '' ? $notas : '—',
        'veterinario' => (string) $r['veterinario'],
    ];
}

api_json(['ok' => true, 'registros' => $out]);
