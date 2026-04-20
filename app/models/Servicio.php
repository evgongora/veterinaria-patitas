<?php

declare(strict_types=1);

/**
 * Modelo SERVICIO — catálogo y mutaciones staff.
 */
final class Servicio
{
    public const ICONOS_FALLBACK = ['🩺', '💉', '💊', '🛁', '🐾', '✂️', '📋', '🏥', '🔬', '🚨'];


    public static function filaDesdeRow(array $r, ?array $iconos = null): array
    {
        $iconos = $iconos ?? self::ICONOS_FALLBACK;
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

    public static function parseId(mixed $raw): int
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

    public static function listarTodos(PDO $pdo): array
    {
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
            $lista[] = self::filaDesdeRow($r);
        }

        return $lista;
    }

    public static function crear(PDO $pdo, array $body): array
    {
        $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $descripcion = isset($body['descripcion']) ? trim((string) $body['descripcion']) : '';
        $precio = isset($body['precio']) ? (float) $body['precio'] : 0.0;
        $duracion = isset($body['duracionMin']) ? (int) $body['duracionMin'] : 0;
        $estadoTxt = isset($body['estado']) ? trim((string) $body['estado']) : 'Activo';
        $estadoId = strcasecmp($estadoTxt, 'Activo') === 0 ? 1 : 2;
        $iconoRaw = isset($body['icono']) ? trim((string) $body['icono']) : '🐾';
        $icono = mb_substr($iconoRaw, 0, 32);

        if ($nombre === '' || $descripcion === '') {
            return ['ok' => false, 'error' => 'Nombre y descripción son obligatorios', 'code' => 400];
        }
        if ($precio < 0 || $duracion <= 0) {
            return ['ok' => false, 'error' => 'Precio y duración no válidos', 'code' => 400];
        }

        $nextId = (int) $pdo->query('SELECT COALESCE(MAX(SERVICIO_ID_PK), 0) + 1 FROM SERVICIO')->fetchColumn();
        if ($nextId <= 0) {
            return ['ok' => false, 'error' => 'No se pudo generar el id', 'code' => 500];
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
            return ['ok' => false, 'error' => 'No se pudo leer el servicio creado', 'code' => 500];
        }

        return ['ok' => true, 'servicio' => self::filaDesdeRow($row)];
    }


    public static function actualizar(PDO $pdo, array $body): array
    {
        $idNum = self::parseId($body['servicioId'] ?? $body['id'] ?? '');
        if ($idNum <= 0) {
            return ['ok' => false, 'error' => 'servicioId inválido', 'code' => 400];
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
            return ['ok' => false, 'error' => 'Nombre y descripción son obligatorios', 'code' => 400];
        }
        if ($precio < 0 || $duracion <= 0) {
            return ['ok' => false, 'error' => 'Precio y duración no válidos', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM SERVICIO WHERE SERVICIO_ID_PK = ? LIMIT 1');
        $st->execute([$idNum]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Servicio no encontrado', 'code' => 404];
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

        return ['ok' => true, 'servicio' => $row ? self::filaDesdeRow($row) : null];
    }


    public static function desactivar(PDO $pdo, string $idRaw): array
    {
        $idNum = self::parseId($idRaw);
        if ($idNum <= 0) {
            return ['ok' => false, 'error' => 'Parámetro id requerido', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM SERVICIO WHERE SERVICIO_ID_PK = ? LIMIT 1');
        $st->execute([$idNum]);
        if ($st->fetchColumn() === false) {
            return ['ok' => false, 'error' => 'Servicio no encontrado', 'code' => 404];
        }

        $upd = $pdo->prepare('UPDATE SERVICIO SET ESTADOTB_ID_FK = 2 WHERE SERVICIO_ID_PK = ?');
        $upd->execute([$idNum]);

        return ['ok' => true];
    }
}
