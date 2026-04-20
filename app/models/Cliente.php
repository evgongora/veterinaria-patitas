<?php

declare(strict_types=1);

/**
 * Modelo CLIENTE — gestión staff (CRUD) y utilidades de nombre/cédula.
 */
final class Cliente
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


    public static function parseApellido2(?string $ap2Raw): array
    {
        $ap2Raw = (string) $ap2Raw;
        if ($ap2Raw === '') {
            return ['nombreCompleto' => '', 'cedulaRef' => ''];
        }
        if (preg_match('/^(.*?)\s*·\s*Ref:\s*(.+)$/us', $ap2Raw, $m)) {
            return ['nombreCompleto' => trim($m[1]), 'cedulaRef' => trim($m[2])];
        }

        return ['nombreCompleto' => $ap2Raw, 'cedulaRef' => ''];
    }


    public static function findForStaff(PDO $pdo, int $id): array
    {
        $st = $pdo->prepare(
            'SELECT c.CLIENTE_ID_PK AS id, c.NOMBRE, c.APELLIDO_1, c.APELLIDO_2, c.TELEFONO, u.EMAIL
             FROM CLIENTE c
             LEFT JOIN USUARIO u ON u.USUARIO_ID_PK = c.USUARIO_ID_FK
             WHERE c.CLIENTE_ID_PK = ? LIMIT 1'
        );
        $st->execute([$id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            return ['ok' => false, 'error' => 'Cliente no encontrado', 'code' => 404];
        }
        $parsed = self::parseApellido2($r['APELLIDO_2'] ?? null);
        $nomMedio = trim((string) $r['NOMBRE'] . ' ' . $r['APELLIDO_1'] . ' ' . $parsed['nombreCompleto']);

        return [
            'ok' => true,
            'cliente' => [
                'id' => (int) $r['id'],
                'nombre' => $nomMedio,
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
                'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'CL-' . str_pad((string) $r['id'], 4, '0', STR_PAD_LEFT),
            ],
        ];
    }


    public static function listForStaff(PDO $pdo): array
    {
        $sql = <<<'SQL'
SELECT
    c.CLIENTE_ID_PK AS id,
    c.NOMBRE,
    c.APELLIDO_1,
    c.APELLIDO_2,
    c.TELEFONO,
    u.EMAIL
FROM CLIENTE c
LEFT JOIN USUARIO u ON u.USUARIO_ID_PK = c.USUARIO_ID_FK
ORDER BY c.NOMBRE ASC
SQL;

        $rows = $pdo->query($sql)->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $parsed = self::parseApellido2($r['APELLIDO_2'] ?? null);
            $nom = trim((string) $r['NOMBRE'] . ' ' . $r['APELLIDO_1'] . ' ' . $parsed['nombreCompleto']);
            $out[] = [
                'id' => (int) $r['id'],
                'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'CL-' . str_pad((string) $r['id'], 4, '0', STR_PAD_LEFT),
                'nombre' => $nom,
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
            ];
        }

        return $out;
    }


    public static function createByStaff(PDO $pdo, array $body): array
    {
        $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
        $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
        $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '') {
            return ['ok' => false, 'error' => 'Nombre, cédula, teléfono y correo son obligatorios', 'code' => 400];
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
            $cid = (int) $pdo->query('SELECT COALESCE(MAX(CLIENTE_ID_PK), 0) + 1 FROM CLIENTE')->fetchColumn();

            $insU = $pdo->prepare(
                'INSERT INTO USUARIO (USUARIO_ID_PK, ESTADO_ID_FK, ROL_FK, EMAIL, CONTRASENA) VALUES (?, 1, 3, ?, ?)'
            );
            $insU->execute([$uid, $email, $hash]);

            $insC = $pdo->prepare(
                'INSERT INTO CLIENTE (CLIENTE_ID_PK, USUARIO_ID_FK, DIRECCION_ID_FK, NOMBRE, APELLIDO_1, APELLIDO_2, TELEFONO) VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $insC->execute([$cid, $uid, $direccionDefault, $nombre, $ap1, $ap2, $telefono]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'No se pudo crear el cliente', 'code' => 500];
        }

        return ['ok' => true, 'clienteId' => $cid];
    }


    public static function updateByStaff(PDO $pdo, array $body): array
    {
        $clienteId = isset($body['clienteId']) ? (int) $body['clienteId'] : 0;
        if ($clienteId <= 0) {
            return ['ok' => false, 'error' => 'Falta clienteId', 'code' => 400];
        }

        $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
        $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
        $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';

        if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '') {
            return ['ok' => false, 'error' => 'Datos incompletos', 'code' => 400];
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Correo no válido', 'code' => 400];
        }

        $st = $pdo->prepare(
            'SELECT c.USUARIO_ID_FK FROM CLIENTE c WHERE c.CLIENTE_ID_PK = ? LIMIT 1'
        );
        $st->execute([$clienteId]);
        $usuarioId = $st->fetchColumn();
        if ($usuarioId === false) {
            return ['ok' => false, 'error' => 'Cliente no encontrado', 'code' => 404];
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
            $updC = $pdo->prepare(
                'UPDATE CLIENTE SET NOMBRE = ?, APELLIDO_1 = ?, APELLIDO_2 = ?, TELEFONO = ? WHERE CLIENTE_ID_PK = ?'
            );
            $updC->execute([$nombre, $ap1, $ap2, $telefono, $clienteId]);

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
