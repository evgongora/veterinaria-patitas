<?php

declare(strict_types=1);

/**
 * Modelo MEDICAMENTO — inventario clínico.
 */
final class Medicamento
{
    public static function mapFila(array $r): array
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


    public static function tiposActivos(PDO $pdo): array
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


    public static function listar(PDO $pdo): array
    {
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
            $out[] = self::mapFila($r);
        }

        return ['ok' => true, 'items' => $out];
    }


    public static function obtener(PDO $pdo, int $idUno): array
    {
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
            return ['ok' => false, 'error' => 'Medicamento no encontrado', 'code' => 404];
        }

        return [
            'ok' => true,
            'item' => self::mapFila($r),
            'tipos' => self::tiposActivos($pdo),
        ];
    }


    public static function crear(PDO $pdo, array $body): array
    {
        $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $cantidad = isset($body['cantidad']) ? (int) $body['cantidad'] : 0;
        $tipoId = isset($body['tipoId']) ? (int) $body['tipoId'] : 0;

        if ($nombre === '' || $tipoId <= 0) {
            return ['ok' => false, 'error' => 'Nombre y tipo de medicamento son obligatorios', 'code' => 400];
        }
        if ($cantidad < 0) {
            return ['ok' => false, 'error' => 'La cantidad no puede ser negativa', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM TIPO_DE_MEDICAMENTO WHERE TIPO_DE_MEDICAMENTO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
        $st->execute([$tipoId]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Tipo de medicamento no válido', 'code' => 400];
        }

        $nuevoId = (int) $pdo->query('SELECT COALESCE(MAX(MEDICAMENTO_CODIGO_PK), 0) + 1 FROM MEDICAMENTO')->fetchColumn();

        $ins = $pdo->prepare(
            'INSERT INTO MEDICAMENTO (MEDICAMENTO_CODIGO_PK, ESTADO_ID_FK, TIPO_DE_MEDICAMENTO_FK, NOMBRE, CANTIDAD_DISPONIBLE) VALUES (?, 1, ?, ?, ?)'
        );
        $ins->execute([$nuevoId, $tipoId, $nombre, $cantidad]);

        return ['ok' => true, 'idNum' => $nuevoId];
    }


    public static function actualizar(PDO $pdo, array $body): array
    {
        $idNum = isset($body['idNum']) ? (int) $body['idNum'] : 0;
        $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $cantidad = isset($body['cantidad']) ? (int) $body['cantidad'] : 0;
        $tipoId = isset($body['tipoId']) ? (int) $body['tipoId'] : 0;

        if ($idNum <= 0) {
            return ['ok' => false, 'error' => 'Falta idNum', 'code' => 400];
        }
        if ($nombre === '' || $tipoId <= 0) {
            return ['ok' => false, 'error' => 'Nombre y tipo son obligatorios', 'code' => 400];
        }
        if ($cantidad < 0) {
            return ['ok' => false, 'error' => 'La cantidad no puede ser negativa', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM TIPO_DE_MEDICAMENTO WHERE TIPO_DE_MEDICAMENTO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
        $st->execute([$tipoId]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Tipo de medicamento no válido', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM MEDICAMENTO WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
        $st->execute([$idNum]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Medicamento no encontrado', 'code' => 404];
        }

        $upd = $pdo->prepare(
            'UPDATE MEDICAMENTO SET NOMBRE = ?, CANTIDAD_DISPONIBLE = ?, TIPO_DE_MEDICAMENTO_FK = ? WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1'
        );
        $upd->execute([$nombre, $cantidad, $tipoId, $idNum]);

        return ['ok' => true];
    }


    public static function bajaLogica(PDO $pdo, int $idNum): array
    {
        if ($idNum <= 0) {
            return ['ok' => false, 'error' => 'Falta idNum', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM MEDICAMENTO WHERE MEDICAMENTO_CODIGO_PK = ? AND ESTADO_ID_FK = 1 LIMIT 1');
        $st->execute([$idNum]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Medicamento no encontrado', 'code' => 404];
        }

        $pdo->prepare('UPDATE MEDICAMENTO SET ESTADO_ID_FK = 2 WHERE MEDICAMENTO_CODIGO_PK = ?')->execute([$idNum]);

        return ['ok' => true];
    }
}
