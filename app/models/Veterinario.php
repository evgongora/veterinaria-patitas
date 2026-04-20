<?php

declare(strict_types=1);

/**
 * Modelo VETERINARIO — listados y CRUD administrador.
 */
final class Veterinario
{

    public static function splitNombreCompleto(string $nombreCompleto, string $cedula): array
    {
        $partes = preg_split('/\s+/', trim($nombreCompleto), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $nombre = $partes[0] ?? 'Usuario';
        $ap1 = $partes[1] ?? '.';
        $rest = count($partes) > 2 ? implode(' ', array_slice($partes, 2)) : '';
        $ref = '· Ref: ' . $cedula;
        $ap2 = trim($rest === '' ? $ref : $rest . ' ' . $ref);

        return [$nombre, $ap1, $ap2];
    }


    public static function parseSegundoApellido(?string $seg): array
    {
        $seg = (string) $seg;
        if ($seg === '') {
            return ['nombreResto' => '', 'cedulaRef' => ''];
        }
        if (preg_match('/^(.*?)\s*·\s*Ref:\s*(.+)$/us', $seg, $m)) {
            return ['nombreResto' => trim($m[1]), 'cedulaRef' => trim($m[2])];
        }

        return ['nombreResto' => $seg, 'cedulaRef' => ''];
    }


    public static function findById(PDO $pdo, int $id): array
    {
        $st = $pdo->prepare(
            'SELECT v.VETERINARIO_ID_PK AS id, v.NOMBRE, v.PRIMER_APELLIDO, v.SEGUNDO_APELLIDO, v.ESPECIALIDAD, v.TELEFONO, u.EMAIL
             FROM VETERINARIO v
             JOIN USUARIO u ON u.USUARIO_ID_PK = v.USUARIO_ID_FK
             WHERE v.VETERINARIO_ID_PK = ? LIMIT 1'
        );
        $st->execute([$id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            return ['ok' => false, 'error' => 'Veterinario no encontrado', 'code' => 404];
        }
        $parsed = self::parseSegundoApellido($r['SEGUNDO_APELLIDO'] ?? null);
        $nomCompleto = trim(
            (string) $r['NOMBRE'] . ' ' . $r['PRIMER_APELLIDO'] . ' ' . $parsed['nombreResto']
        );

        return [
            'ok' => true,
            'veterinario' => [
                'id' => (int) $r['id'],
                'nombre' => $nomCompleto,
                'especialidad' => (string) ($r['ESPECIALIDAD'] ?? ''),
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
                'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'VET-' . str_pad((string) $r['id'], 3, '0', STR_PAD_LEFT),
            ],
        ];
    }


    public static function listAll(PDO $pdo): array
    {
        $sql = <<<'SQL'
SELECT
    v.VETERINARIO_ID_PK AS id,
    v.NOMBRE,
    v.PRIMER_APELLIDO,
    v.SEGUNDO_APELLIDO,
    v.ESPECIALIDAD,
    v.TELEFONO,
    u.EMAIL
FROM VETERINARIO v
JOIN USUARIO u ON u.USUARIO_ID_PK = v.USUARIO_ID_FK
ORDER BY v.NOMBRE ASC
SQL;

        $rows = $pdo->query($sql)->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $parsed = self::parseSegundoApellido($r['SEGUNDO_APELLIDO'] ?? null);
            $nom = trim((string) $r['NOMBRE'] . ' ' . $r['PRIMER_APELLIDO'] . ' ' . $parsed['nombreResto']);
            $out[] = [
                'id' => (int) $r['id'],
                'nombreCompleto' => $nom,
                'especialidad' => (string) ($r['ESPECIALIDAD'] ?? ''),
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
            ];
        }

        return $out;
    }


    public static function create(PDO $pdo, array $body): array
    {
        $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
        $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
        $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
        $especialidad = isset($body['especialidad']) ? trim((string) $body['especialidad']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '' || $especialidad === '') {
            return ['ok' => false, 'error' => 'Todos los campos obligatorios incluido especialidad', 'code' => 400];
        }
        if (strlen($password) < 4) {
            return ['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres', 'code' => 400];
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Correo no válido', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) LIMIT 1');
        $st->execute([$email]);
        if ($st->fetchColumn() !== false) {
            return ['ok' => false, 'error' => 'Ese correo ya está registrado', 'code' => 409];
        }

        [$nombre, $ap1, $ap2] = self::splitNombreCompleto($nombreCompleto, $cedula);

        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false) {
            return ['ok' => false, 'error' => 'No se pudo procesar la contraseña', 'code' => 500];
        }

        $direccionDefault = 1;

        $pdo->beginTransaction();
        try {
            $uid = (int) $pdo->query('SELECT COALESCE(MAX(USUARIO_ID_PK), 0) + 1 FROM USUARIO')->fetchColumn();
            $vid = (int) $pdo->query('SELECT COALESCE(MAX(VETERINARIO_ID_PK), 0) + 1 FROM VETERINARIO')->fetchColumn();

            $insU = $pdo->prepare(
                'INSERT INTO USUARIO (USUARIO_ID_PK, ESTADO_ID_FK, ROL_FK, EMAIL, CONTRASENA) VALUES (?, 1, 2, ?, ?)'
            );
            $insU->execute([$uid, $email, $hash]);

            $insV = $pdo->prepare(
                'INSERT INTO VETERINARIO (VETERINARIO_ID_PK, USUARIO_ID_FK, DIRECCION_ID_FK, NOMBRE, PRIMER_APELLIDO, SEGUNDO_APELLIDO, TELEFONO, ESPECIALIDAD) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insV->execute([$vid, $uid, $direccionDefault, $nombre, $ap1, $ap2, $telefono, $especialidad]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'No se pudo crear el veterinario', 'code' => 500];
        }

        return ['ok' => true, 'veterinarioId' => $vid];
    }


    public static function update(PDO $pdo, array $body): array
    {
        $vetId = isset($body['veterinarioId']) ? (int) $body['veterinarioId'] : 0;
        if ($vetId <= 0) {
            return ['ok' => false, 'error' => 'Falta veterinarioId', 'code' => 400];
        }

        $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
        $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
        $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
        $especialidad = isset($body['especialidad']) ? trim((string) $body['especialidad']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '' || $especialidad === '') {
            return ['ok' => false, 'error' => 'Datos incompletos', 'code' => 400];
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Correo no válido', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT USUARIO_ID_FK FROM VETERINARIO WHERE VETERINARIO_ID_PK = ? LIMIT 1');
        $st->execute([$vetId]);
        $usuarioId = $st->fetchColumn();
        if ($usuarioId === false) {
            return ['ok' => false, 'error' => 'Veterinario no encontrado', 'code' => 404];
        }
        $usuarioId = (int) $usuarioId;

        $st = $pdo->prepare(
            'SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) AND USUARIO_ID_PK <> ? LIMIT 1'
        );
        $st->execute([$email, $usuarioId]);
        if ($st->fetchColumn() !== false) {
            return ['ok' => false, 'error' => 'Ese correo pertenece a otro usuario', 'code' => 409];
        }

        [$nombre, $ap1, $ap2] = self::splitNombreCompleto($nombreCompleto, $cedula);

        $pdo->beginTransaction();
        try {
            $updV = $pdo->prepare(
                'UPDATE VETERINARIO SET NOMBRE = ?, PRIMER_APELLIDO = ?, SEGUNDO_APELLIDO = ?, TELEFONO = ?, ESPECIALIDAD = ? WHERE VETERINARIO_ID_PK = ?'
            );
            $updV->execute([$nombre, $ap1, $ap2, $telefono, $especialidad, $vetId]);

            $updU = $pdo->prepare('UPDATE USUARIO SET EMAIL = ? WHERE USUARIO_ID_PK = ?');
            $updU->execute([$email, $usuarioId]);

            if ($password !== '') {
                if (strlen($password) < 4) {
                    $pdo->rollBack();

                    return ['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres', 'code' => 400];
                }
                $hash = password_hash($password, PASSWORD_DEFAULT);
                if ($hash === false) {
                    $pdo->rollBack();

                    return ['ok' => false, 'error' => 'No se pudo procesar la contraseña', 'code' => 500];
                }
                $updP = $pdo->prepare('UPDATE USUARIO SET CONTRASENA = ? WHERE USUARIO_ID_PK = ?');
                $updP->execute([$hash, $usuarioId]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'No se pudo actualizar', 'code' => 500];
        }

        return ['ok' => true];
    }
}
