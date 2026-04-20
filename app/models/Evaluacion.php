<?php

declare(strict_types=1);

/**
 * Modelo EVALUACION — valoraciones.
 */
final class Evaluacion
{
    public static function listarPublicas(PDO $pdo, int $limit): array
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

    public static function listarStaff(PDO $pdo, int $limit): array
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


    public static function listarPorUsuario(PDO $pdo, int $uid): array
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


    public static function crear(PDO $pdo, int $uid, array $body): array
    {
        $rating = isset($body['rating']) ? (int) $body['rating'] : 0;
        $comentario = isset($body['comentario']) ? trim((string) $body['comentario']) : '';

        if ($rating < 1 || $rating > 5) {
            return ['ok' => false, 'error' => 'Puntuación inválida', 'code' => 400];
        }
        if (strlen($comentario) < 3) {
            return ['ok' => false, 'error' => 'Comentario muy corto', 'code' => 400];
        }

        try {
            $ins = $pdo->prepare(
                'INSERT INTO EVALUACION (USUARIO_ID_FK, RATING, COMENTARIO) VALUES (?, ?, ?)'
            );
            $ins->execute([$uid, $rating, $comentario]);
            $newId = (int) $pdo->lastInsertId();
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'error' => 'No se pudo guardar. Verifica la tabla EVALUACION en MySQL.',
                'code' => 503,
            ];
        }

        return ['ok' => true, 'id' => $newId];
    }


    public static function actualizar(PDO $pdo, int $uid, array $body): array
    {
        $id = isset($body['id']) ? (int) $body['id'] : 0;
        $rating = isset($body['rating']) ? (int) $body['rating'] : 0;
        $comentario = isset($body['comentario']) ? trim((string) $body['comentario']) : '';

        if ($id <= 0) {
            return ['ok' => false, 'error' => 'Falta id de evaluación', 'code' => 400];
        }
        if ($rating < 1 || $rating > 5) {
            return ['ok' => false, 'error' => 'Puntuación inválida', 'code' => 400];
        }
        if (strlen($comentario) < 3) {
            return ['ok' => false, 'error' => 'Comentario muy corto', 'code' => 400];
        }

        $st = $pdo->prepare(
            'UPDATE EVALUACION SET RATING = ?, COMENTARIO = ? WHERE EVALUACION_ID_PK = ? AND USUARIO_ID_FK = ?'
        );
        $st->execute([$rating, $comentario, $id, $uid]);
        if ($st->rowCount() === 0) {
            return ['ok' => false, 'error' => 'Evaluación no encontrada o sin permiso', 'code' => 404];
        }

        return ['ok' => true];
    }


    public static function eliminar(PDO $pdo, int $uid, int $id): array
    {
        if ($id <= 0) {
            return ['ok' => false, 'error' => 'Falta id', 'code' => 400];
        }

        $st = $pdo->prepare('DELETE FROM EVALUACION WHERE EVALUACION_ID_PK = ? AND USUARIO_ID_FK = ?');
        $st->execute([$id, $uid]);
        if ($st->rowCount() === 0) {
            return ['ok' => false, 'error' => 'Evaluación no encontrada o sin permiso', 'code' => 404];
        }

        return ['ok' => true];
    }
}
