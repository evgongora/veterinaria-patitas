<?php
/**
 * Inventario (MEDICAMENTO) — administrador y veterinario: CRUD.
 * Cliente (rol 3): sin acceso.
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

/**
 * @param array<string, mixed> $r
 * @return array<string, mixed>
 */
function patitas_map_medicamento(array $r): array
{
    $id = (int) ($r['id'] ?? 0);
    $nombre = (string) ($r['nombre'] ?? '');
    $c = (int) ($r['cantidad'] ?? 0);
    $tipo = (string) ($r['tipo'] ?? '');
    $tipoId = (int) ($r['tipoId'] ?? 0);
    if ($c > 50) {
        $estado = 'Normal';
    } elseif ($c > 10) {
        $estado = 'Bajo';
    } else {
        $estado = 'Critico';
    }

    return [
        'id' => 'INV-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT),
        'idNum' => $id,
        'nombre' => $nombre,
        'cantidad' => $c,
        'vencimiento' => null,
        'proveedor' => $tipo,
        'estado' => $estado,
        'tipoId' => $tipoId,
    ];
}

/**
 * @return list<array{id: int, nombre: string}>
 */
function patitas_tipos_medicamento(PDO $pdo): array
{
    $sql = <<<'SQL'
SELECT TIPO_DE_MEDICAMENTO_PK AS id, DESCRIPCION AS nombre
FROM TIPO_DE_MEDICAMENTO
WHERE ESTADO_ID_FK = 1
ORDER BY TIPO_DE_MEDICAMENTO_PK ASC
SQL;
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $row) {
        $out[] = [
            'id' => (int) $row['id'],
            'nombre' => (string) $row['nombre'],
        ];
    }

    return $out;
}

if ($method === 'GET') {
    api_require_login();
    api_require_staff();

    if (isset($_GET['tipos']) && (string) $_GET['tipos'] === '1') {
        api_json(['ok' => true, 'tipos' => patitas_tipos_medicamento($pdo)]);
    }

    $idUno = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($idUno > 0) {
        $sql = <<<'SQL'
SELECT
    m.MEDICAMENTO_CODIGO_PK AS id,
    m.NOMBRE AS nombre,
    m.CANTIDAD_DISPONIBLE AS cantidad,
    m.TIPO_DE_MEDICAMENTO_FK AS tipoId,
    t.DESCRIPCION AS tipo
FROM MEDICAMENTO m
JOIN TIPO_DE_MEDICAMENTO t ON t.TIPO_DE_MEDICAMENTO_PK = m.TIPO_DE_MEDICAMENTO_FK
WHERE m.MEDICAMENTO_CODIGO_PK = ? AND m.ESTADO_ID_FK = 1
LIMIT 1
SQL;
        $st = $pdo->prepare($sql);
        $st->execute([$idUno]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            api_json(['ok' => false, 'error' => 'Medicamento no encontrado'], 404);
        }
        $item = patitas_map_medicamento($r);
        api_json(['ok' => true, 'item' => $item, 'tipos' => patitas_tipos_medicamento($pdo)]);
    }

    $sql = <<<'SQL'
SELECT
    m.MEDICAMENTO_CODIGO_PK AS id,
    m.NOMBRE AS nombre,
    m.CANTIDAD_DISPONIBLE AS cantidad,
    m.TIPO_DE_MEDICAMENTO_FK AS tipoId,
    t.DESCRIPCION AS tipo
FROM MEDICAMENTO m
JOIN TIPO_DE_MEDICAMENTO t ON t.TIPO_DE_MEDICAMENTO_PK = m.TIPO_DE_MEDICAMENTO_FK
WHERE m.ESTADO_ID_FK = 1
ORDER BY m.NOMBRE ASC
SQL;

    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
        $out[] = patitas_map_medicamento($r);
    }

    api_json(['ok' => true, 'items' => $out]);
}

if ($method === 'POST') {
    api_require_login();
    api_require_staff();
    $body = api_json_body();

    $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $cantidad = isset($body['cantidad']) ? (int) $body['cantidad'] : 0;
    $tipoId = isset($body['tipoId']) ? (int) $body['tipoId'] : 0;

    if ($nombre === '' || $tipoId <= 0) {
        api_json(['ok' => false, 'error' => 'Nombre y tipo de medicamento son obligatorios'], 400);
    }
    if ($cantidad < 0) {
        api_json(['ok' => false, 'error' => 'La cantidad no puede ser negativa'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM TIPO_DE_MEDICAMENTO WHERE TIPO_DE_MEDICAMENTO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
    $st->execute([$tipoId]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Tipo de medicamento no válido'], 400);
    }

    $nuevoId = (int) $pdo->query('SELECT COALESCE(MAX(MEDICAMENTO_CODIGO_PK), 0) + 1 FROM MEDICAMENTO')->fetchColumn();

    $ins = $pdo->prepare(
        'INSERT INTO MEDICAMENTO (MEDICAMENTO_CODIGO_PK, ESTADO_ID_FK, TIPO_DE_MEDICAMENTO_FK, NOMBRE, CANTIDAD_DISPONIBLE) VALUES (?, 1, ?, ?, ?)'
    );
    $ins->execute([$nuevoId, $tipoId, $nombre, $cantidad]);

    api_json(['ok' => true, 'idNum' => $nuevoId]);
}

if ($method === 'PUT') {
    api_require_login();
    api_require_staff();
    $body = api_json_body();

    $idNum = isset($body['idNum']) ? (int) $body['idNum'] : 0;
    $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $cantidad = isset($body['cantidad']) ? (int) $body['cantidad'] : 0;
    $tipoId = isset($body['tipoId']) ? (int) $body['tipoId'] : 0;

    if ($idNum <= 0) {
        api_json(['ok' => false, 'error' => 'Falta idNum'], 400);
    }
    if ($nombre === '' || $tipoId <= 0) {
        api_json(['ok' => false, 'error' => 'Nombre y tipo son obligatorios'], 400);
    }
    if ($cantidad < 0) {
        api_json(['ok' => false, 'error' => 'La cantidad no puede ser negativa'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM TIPO_DE_MEDICAMENTO WHERE TIPO_DE_MEDICAMENTO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
    $st->execute([$tipoId]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Tipo de medicamento no válido'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM MEDICAMENTO WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
    $st->execute([$idNum]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Medicamento no encontrado'], 404);
    }

    $upd = $pdo->prepare(
        'UPDATE MEDICAMENTO SET NOMBRE = ?, CANTIDAD_DISPONIBLE = ?, TIPO_DE_MEDICAMENTO_FK = ? WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1'
    );
    $upd->execute([$nombre, $cantidad, $tipoId, $idNum]);

    api_json(['ok' => true]);
}

if ($method === 'DELETE') {
    api_require_login();
    api_require_staff();

    $idNum = isset($_GET['idNum']) ? (int) $_GET['idNum'] : 0;
    if ($idNum <= 0) {
        api_json(['ok' => false, 'error' => 'Falta idNum'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM MEDICAMENTO WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
    $st->execute([$idNum]);
    if ($st->fetchColumn() === false) {
        api_json(['ok' => false, 'error' => 'Medicamento no encontrado'], 404);
    }

    $pdo->prepare('UPDATE MEDICAMENTO SET ESTADO_ID_FK = 2 WHERE MEDICAMENTO_CODIGO_PK = ?')->execute([$idNum]);

    api_json(['ok' => true]);
}

api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
