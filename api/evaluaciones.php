<?php
/**
 * GET: staff → todas (lectura); cliente → públicas + propias; sin sesión → solo públicas anonimizadas.
 * POST: crear — solo clientes.
 * PUT: actualizar — solo cliente dueño.
 * DELETE: eliminar — solo cliente dueño (?id=).
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
 * @return list<array<string, mixed>>
 */
function patitas_evaluaciones_publicas(PDO $pdo, int $limit): array
{
    if ($limit < 1) {
        $limit = 30;
    }
    if ($limit > 100) {
        $limit = 100;
    }

    $sql = <<<'SQL'
SELECT
    e.EVALUACION_ID_PK AS id,
    e.RATING AS rating,
    e.COMENTARIO AS comentario,
    e.FECHA_CREADO AS fecha_creado,
    COALESCE(
        NULLIF(TRIM(c.NOMBRE), ''),
        NULLIF(TRIM(v.NOMBRE), ''),
        'Usuario'
    ) AS autor
FROM EVALUACION e
JOIN USUARIO u ON u.USUARIO_ID_PK = e.USUARIO_ID_FK
LEFT JOIN CLIENTE c ON c.USUARIO_ID_FK = u.USUARIO_ID_PK
LEFT JOIN VETERINARIO v ON v.USUARIO_ID_FK = u.USUARIO_ID_PK
ORDER BY e.FECHA_CREADO DESC
LIMIT ?
SQL;
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $limit, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['rating'] = (int) $r['rating'];
    }
    unset($r);

    return $rows;
}

/**
 * @return list<array<string, mixed>>
 */
function patitas_evaluaciones_staff(PDO $pdo, int $limit): array
{
    if ($limit < 1) {
        $limit = 100;
    }
    if ($limit > 200) {
        $limit = 200;
    }

    $sql = <<<'SQL'
SELECT
    e.EVALUACION_ID_PK AS id,
    e.RATING AS rating,
    e.COMENTARIO AS comentario,
    e.FECHA_CREADO AS fecha_creado,
    u.USUARIO_ID_PK AS usuarioId,
    u.EMAIL AS email,
    COALESCE(
        NULLIF(TRIM(CONCAT(TRIM(c.NOMBRE), ' ', TRIM(c.APELLIDO_1))), ''),
        NULLIF(TRIM(CONCAT(TRIM(v.NOMBRE), ' ', TRIM(v.PRIMER_APELLIDO))), ''),
        u.EMAIL
    ) AS nombreCompleto
FROM EVALUACION e
JOIN USUARIO u ON u.USUARIO_ID_PK = e.USUARIO_ID_FK
LEFT JOIN CLIENTE c ON c.USUARIO_ID_FK = u.USUARIO_ID_PK
LEFT JOIN VETERINARIO v ON v.USUARIO_ID_FK = u.USUARIO_ID_PK
ORDER BY e.FECHA_CREADO DESC
LIMIT ?
SQL;
    $st = $pdo->prepare($sql);
    $st->bindValue(1, $limit, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['rating'] = (int) $r['rating'];
        $r['usuarioId'] = (int) $r['usuarioId'];
    }
    unset($r);

    return $rows;
}

/**
 * @return list<array<string, mixed>>
 */
function patitas_evaluaciones_mias(PDO $pdo, int $uid): array
{
    $sql = <<<'SQL'
SELECT
    e.EVALUACION_ID_PK AS id,
    e.RATING AS rating,
    e.COMENTARIO AS comentario,
    e.FECHA_CREADO AS fecha_creado
FROM EVALUACION e
WHERE e.USUARIO_ID_FK = ?
ORDER BY e.FECHA_CREADO DESC
SQL;
    $st = $pdo->prepare($sql);
    $st->execute([$uid]);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
        $r['id'] = (int) $r['id'];
        $r['rating'] = (int) $r['rating'];
    }
    unset($r);

    return $rows;
}

if ($method === 'GET') {
    $limitStaff = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
    $limitPublic = isset($_GET['limitPublic']) ? (int) $_GET['limitPublic'] : 25;
    if ($limitPublic < 1) {
        $limitPublic = 30;
    }
    if ($limitPublic > 100) {
        $limitPublic = 100;
    }

    if (api_session_user_id() !== null && api_is_staff()) {
        api_require_staff();
        try {
            $rows = patitas_evaluaciones_staff($pdo, $limitStaff);
            api_json(['ok' => true, 'vista' => 'staff', 'evaluaciones' => $rows]);
        } catch (Throwable $e) {
            api_json([
                'ok' => false,
                'error' => 'No se pudieron cargar las evaluaciones.',
                'evaluaciones' => [],
            ], 503);
        }
    }

    if (api_session_user_id() !== null && api_is_cliente()) {
        $uid = api_require_login();
        try {
            $publicas = patitas_evaluaciones_publicas($pdo, $limitPublic);
            $mias = patitas_evaluaciones_mias($pdo, $uid);
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
        $publicas = patitas_evaluaciones_publicas($pdo, $limitPublic);
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

    $body = api_json_body();
    $rating = isset($body['rating']) ? (int) $body['rating'] : 0;
    $comentario = isset($body['comentario']) ? trim((string) $body['comentario']) : '';

    if ($rating < 1 || $rating > 5) {
        api_json(['ok' => false, 'error' => 'Puntuación inválida'], 400);
    }
    if (strlen($comentario) < 3) {
        api_json(['ok' => false, 'error' => 'Comentario muy corto'], 400);
    }

    try {
        $ins = $pdo->prepare(
            'INSERT INTO EVALUACION (USUARIO_ID_FK, RATING, COMENTARIO) VALUES (?, ?, ?)'
        );
        $ins->execute([$uid, $rating, $comentario]);
        $newId = (int) $pdo->lastInsertId();
    } catch (Throwable $e) {
        api_json([
            'ok' => false,
            'error' => 'No se pudo guardar. Verifica la tabla EVALUACION en MySQL.',
        ], 503);
    }

    api_json(['ok' => true, 'id' => $newId]);
}

if ($method === 'PUT') {
    api_require_cliente();
    $uid = (int) api_require_login();

    $body = api_json_body();
    $id = isset($body['id']) ? (int) $body['id'] : 0;
    $rating = isset($body['rating']) ? (int) $body['rating'] : 0;
    $comentario = isset($body['comentario']) ? trim((string) $body['comentario']) : '';

    if ($id <= 0) {
        api_json(['ok' => false, 'error' => 'Falta id de evaluación'], 400);
    }
    if ($rating < 1 || $rating > 5) {
        api_json(['ok' => false, 'error' => 'Puntuación inválida'], 400);
    }
    if (strlen($comentario) < 3) {
        api_json(['ok' => false, 'error' => 'Comentario muy corto'], 400);
    }

    $st = $pdo->prepare(
        'UPDATE EVALUACION SET RATING = ?, COMENTARIO = ? WHERE EVALUACION_ID_PK = ? AND USUARIO_ID_FK = ?'
    );
    $st->execute([$rating, $comentario, $id, $uid]);
    if ($st->rowCount() === 0) {
        api_json(['ok' => false, 'error' => 'Evaluación no encontrada o sin permiso'], 404);
    }

    api_json(['ok' => true]);
}

if ($method === 'DELETE') {
    api_require_cliente();
    $uid = (int) api_require_login();

    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    if ($id <= 0) {
        api_json(['ok' => false, 'error' => 'Falta id'], 400);
    }

    $st = $pdo->prepare('DELETE FROM EVALUACION WHERE EVALUACION_ID_PK = ? AND USUARIO_ID_FK = ?');
    $st->execute([$id, $uid]);
    if ($st->rowCount() === 0) {
        api_json(['ok' => false, 'error' => 'Evaluación no encontrada o sin permiso'], 404);
    }

    api_json(['ok' => true]);
}

api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
