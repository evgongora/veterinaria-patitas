<?php
/**
 * GET: catálogo desde MySQL (lectura pública).
 * POST / PUT / DELETE: administración de servicios (administrador y veterinario — api_require_staff).
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos', 'servicios' => []], 503);
}

$iconos = ['🩺', '💉', '💊', '🛁', '🐾', '✂️', '📋', '🏥', '🔬', '🚨'];

/**
 * @return array<string, mixed>
 */
function patitas_servicio_fila(array $r, array $iconos): array
{
    $idNum = (int) $r['SERVICIO_ID_PK'];
    $estadoId = (int) $r['ESTADOTB_ID_FK'];
    $estadoTxt = $estadoId === 1 ? 'Activo' : 'Inactivo';
    $iconoDb = isset($r['ICONO']) ? trim((string) $r['ICONO']) : '';
    $icono = $iconoDb !== '' ? $iconoDb : $iconos[$idNum % max(1, count($iconos))];

    return [
        'id' => 'SRV-' . str_pad((string) $idNum, 3, '0', STR_PAD_LEFT),
        'idNum' => $idNum,
        'nombre' => (string) $r['NOMBRE'],
        'descripcion' => (string) ($r['DESCRIPCION'] ?? ''),
        'precio' => (float) $r['PRECIO'],
        'duracionMin' => (int) ($r['DURACION'] ?? 0),
        'estado' => $estadoTxt,
        'icono' => $icono,
    ];
}

function patitas_parse_servicio_id(mixed $raw): int
{
    $s = trim((string) $raw);
    if ($s === '') {
        return 0;
    }
    if (preg_match('/^SRV-(\d+)$/i', $s, $m)) {
        return (int) $m[1];
    }

    return (int) $s;
}

if ($method === 'GET') {
    $sql = <<<'SQL'
SELECT
    s.SERVICIO_ID_PK,
    s.NOMBRE,
    s.DESCRIPCION,
    s.PRECIO,
    s.DURACION,
    s.ESTADOTB_ID_FK,
    s.ICONO
FROM SERVICIO s
ORDER BY s.SERVICIO_ID_PK ASC
SQL;

    $st = $pdo->query($sql);
    $rows = $st->fetchAll();
    $lista = [];
    foreach ($rows as $r) {
        $lista[] = patitas_servicio_fila($r, $iconos);
    }

    api_json(['ok' => true, 'servicios' => $lista]);
}

api_require_login();

if (! api_is_staff()) {
    api_json(['ok' => false, 'error' => 'Sin permiso'], 403);
}

if ($method === 'POST') {
    $body = api_json_body();
    $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $descripcion = isset($body['descripcion']) ? trim((string) $body['descripcion']) : '';
    $precio = isset($body['precio']) ? (float) $body['precio'] : 0.0;
    $duracion = isset($body['duracionMin']) ? (int) $body['duracionMin'] : 0;
    $estadoTxt = isset($body['estado']) ? trim((string) $body['estado']) : 'Activo';
    $estadoId = strcasecmp($estadoTxt, 'Activo') === 0 ? 1 : 2;
    $iconoRaw = isset($body['icono']) ? trim((string) $body['icono']) : '🐾';
    $icono = mb_substr($iconoRaw, 0, 32);

    if ($nombre === '' || $descripcion === '') {
        api_json(['ok' => false, 'error' => 'Nombre y descripción son obligatorios'], 400);
    }
    if ($precio < 0 || $duracion <= 0) {
        api_json(['ok' => false, 'error' => 'Precio y duración no válidos'], 400);
    }

    $nextId = (int) $pdo->query('SELECT COALESCE(MAX(SERVICIO_ID_PK), 0) + 1 FROM SERVICIO')->fetchColumn();
    if ($nextId <= 0) {
        api_json(['ok' => false, 'error' => 'No se pudo generar el id'], 500);
    }

    $ins = $pdo->prepare(
        'INSERT INTO SERVICIO (SERVICIO_ID_PK, ESTADOTB_ID_FK, NOMBRE, DESCRIPCION, PRECIO, DURACION, ICONO) VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $ins->execute([$nextId, $estadoId, $nombre, $descripcion, $precio, $duracion, $icono]);

    $st = $pdo->prepare(
        'SELECT SERVICIO_ID_PK, NOMBRE, DESCRIPCION, PRECIO, DURACION, ESTADOTB_ID_FK, ICONO FROM SERVICIO WHERE SERVICIO_ID_PK = ?'
    );
    $st->execute([$nextId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (! $row) {
        api_json(['ok' => false, 'error' => 'No se pudo leer el servicio creado'], 500);
    }

    api_json([
        'ok' => true,
        'servicio' => patitas_servicio_fila($row, $iconos),
    ]);
}

if ($method === 'PUT') {
    $body = api_json_body();
    $idNum = patitas_parse_servicio_id($body['servicioId'] ?? $body['id'] ?? '');
    if ($idNum <= 0) {
        api_json(['ok' => false, 'error' => 'servicioId inválido'], 400);
    }

    $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $descripcion = isset($body['descripcion']) ? trim((string) $body['descripcion']) : '';
    $precio = isset($body['precio']) ? (float) $body['precio'] : 0.0;
    $duracion = isset($body['duracionMin']) ? (int) $body['duracionMin'] : 0;
    $estadoTxt = isset($body['estado']) ? trim((string) $body['estado']) : 'Activo';
    $estadoId = strcasecmp($estadoTxt, 'Activo') === 0 ? 1 : 2;
    $iconoRaw = isset($body['icono']) ? trim((string) $body['icono']) : '🐾';
    $icono = mb_substr($iconoRaw, 0, 32);

    if ($nombre === '' || $descripcion === '') {
        api_json(['ok' => false, 'error' => 'Nombre y descripción son obligatorios'], 400);
    }
    if ($precio < 0 || $duracion <= 0) {
        api_json(['ok' => false, 'error' => 'Precio y duración no válidos'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM SERVICIO WHERE SERVICIO_ID_PK = ? LIMIT 1');
    $st->execute([$idNum]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Servicio no encontrado'], 404);
    }

    $upd = $pdo->prepare(
        'UPDATE SERVICIO SET ESTADOTB_ID_FK = ?, NOMBRE = ?, DESCRIPCION = ?, PRECIO = ?, DURACION = ?, ICONO = ? WHERE SERVICIO_ID_PK = ?'
    );
    $upd->execute([$estadoId, $nombre, $descripcion, $precio, $duracion, $icono, $idNum]);

    $st = $pdo->prepare(
        'SELECT SERVICIO_ID_PK, NOMBRE, DESCRIPCION, PRECIO, DURACION, ESTADOTB_ID_FK, ICONO FROM SERVICIO WHERE SERVICIO_ID_PK = ?'
    );
    $st->execute([$idNum]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    api_json([
        'ok' => true,
        'servicio' => $row ? patitas_servicio_fila($row, $iconos) : null,
    ]);
}

if ($method === 'DELETE') {
    $idRaw = $_GET['id'] ?? '';
    $idNum = patitas_parse_servicio_id($idRaw);
    if ($idNum <= 0) {
        api_json(['ok' => false, 'error' => 'Parámetro id requerido'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM SERVICIO WHERE SERVICIO_ID_PK = ? LIMIT 1');
    $st->execute([$idNum]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Servicio no encontrado'], 404);
    }

    /** Desactivar en BD (no borrado físico por posibles citas vinculadas). */
    $upd = $pdo->prepare('UPDATE SERVICIO SET ESTADOTB_ID_FK = 2 WHERE SERVICIO_ID_PK = ?');
    $upd->execute([$idNum]);

    api_json(['ok' => true]);
}

api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
