<?php
/**
 * GET: listado de citas (cliente: solo las suyas; staff: todas)
 * POST: crear cita (solo cliente)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

if ($method === 'GET') {
    api_require_login();

    $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    c.FECHA,
    c.HORA_DE_INICIO,
    c.HORA_DE_FINALIZACION,
    a.NOMBRE AS animal,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    t.DESCRIPCION AS tipo,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
SQL;

    if (api_is_cliente()) {
        $cid = api_cliente_id($pdo);
        if ($cid === null) {
            api_json(['ok' => true, 'citas' => []]);
        }
        $sql .= ' WHERE a.CLIENTE_ID_FK = ? ORDER BY c.FECHA DESC, c.HORA_DE_INICIO DESC';
        $st = $pdo->prepare($sql);
        $st->execute([$cid]);
    } else {
        $sql .= ' ORDER BY c.FECHA DESC, c.HORA_DE_INICIO DESC';
        $st = $pdo->query($sql);
    }

    $rows = $st->fetchAll();
    $out = [];
    foreach ($rows as $r) {
        $hi = substr((string) $r['HORA_DE_INICIO'], 0, 5);
        $hf = $r['HORA_DE_FINALIZACION'] ? substr((string) $r['HORA_DE_FINALIZACION'], 0, 5) : $hi;
        $out[] = [
            'id' => (int) $r['CITA_ID_PK'],
            'animal' => (string) $r['animal'],
            'veterinario' => (string) $r['veterinario'],
            'fecha' => (string) $r['FECHA'],
            'horaInicio' => $hi,
            'horaFin' => $hf,
            'tipo' => (string) $r['tipo'],
            'estado' => (string) $r['estado'],
        ];
    }

    api_json(['ok' => true, 'citas' => $out]);
}

api_require_method('POST');
api_require_login();
api_require_cliente();

$body = api_json_body();
$animalId = isset($body['animalId']) ? (int) $body['animalId'] : 0;
$vetId = isset($body['veterinarioId']) ? (int) $body['veterinarioId'] : 0;
$fecha = isset($body['fecha']) ? trim((string) $body['fecha']) : '';
$horaInicio = isset($body['horaInicio']) ? trim((string) $body['horaInicio']) : '';
$tipoCitaId = isset($body['tipoCitaId']) ? (int) $body['tipoCitaId'] : 1;
$notas = isset($body['notas']) ? trim((string) $body['notas']) : '';

if ($animalId <= 0 || $vetId <= 0 || $fecha === '' || $horaInicio === '') {
    api_json(['ok' => false, 'error' => 'Faltan datos obligatorios'], 400);
}

$cid = api_cliente_id($pdo);
if ($cid === null) {
    api_json(['ok' => false, 'error' => 'Perfil de cliente no encontrado'], 400);
}

$st = $pdo->prepare('SELECT CLIENTE_ID_FK FROM ANIMAL WHERE ANIMAL_ID_PK = ? LIMIT 1');
$st->execute([$animalId]);
$owner = $st->fetchColumn();
if ($owner === false || (int) $owner !== $cid) {
    api_json(['ok' => false, 'error' => 'El animal no pertenece a tu cuenta'], 403);
}

if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
    api_json(['ok' => false, 'error' => 'Fecha inválida (use YYYY-MM-DD)'], 400);
}

if (! preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
    api_json(['ok' => false, 'error' => 'Hora inválida'], 400);
}

$ts = strtotime($fecha . ' ' . $horaInicio . ':00');
if ($ts === false) {
    api_json(['ok' => false, 'error' => 'Fecha u hora no válidas'], 400);
}

$horaFin = date('H:i:s', strtotime('+30 minutes', $ts));

$st = $pdo->query('SELECT COALESCE(MAX(CITA_ID_PK), 0) + 1 AS n FROM CITA');
$nextId = (int) $st->fetchColumn();

$ins = $pdo->prepare(
    'INSERT INTO CITA (CITA_ID_PK, ANIMAL_ID_FK, ESTADO_ID_FK, VETERINARIO_ID_FK, TIPO_DE_CITA_ID_FK, FECHA, HORA_DE_INICIO, HORA_DE_FINALIZACION, NOTAS_VETERINARIO)
     VALUES (?, ?, 3, ?, ?, ?, ?, ?, ?)'
);
$ins->execute([
    $nextId,
    $animalId,
    $vetId,
    $tipoCitaId,
    $fecha,
    $horaInicio . ':00',
    $horaFin,
    $notas !== '' ? $notas : null,
]);

api_json(['ok' => true, 'citaId' => $nextId]);
