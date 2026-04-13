<?php
/**
 * GET: listado o ?id= (solo staff)
 * POST: crear cliente + usuario (solo staff)
 * PUT: actualizar cliente (solo staff)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

/**
 * @return array{0: string, 1: string, 2: ?string}
 */
function patitas_split_cliente_nombre(string $nombreCompleto, string $cedula): array
{
    $partes = preg_split('/\s+/', trim($nombreCompleto), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $nombre = $partes[0] ?? 'Usuario';
    $ap1 = $partes[1] ?? '.';
    $rest = count($partes) > 2 ? implode(' ', array_slice($partes, 2)) : '';
    $ref = '· Ref: ' . $cedula;
    $ap2 = trim($rest === '' ? $ref : $rest . ' ' . $ref);

    return [$nombre, $ap1, $ap2];
}

/**
 * @return array{nombreCompleto: string, cedulaRef: string}
 */
function patitas_parse_cliente_apellido2(?string $ap2Raw): array
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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

if ($method === 'GET') {
    api_require_login();
    api_require_staff();

    $idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($idGet > 0) {
        $st = $pdo->prepare(
            'SELECT c.CLIENTE_ID_PK AS id, c.NOMBRE, c.APELLIDO_1, c.APELLIDO_2, c.TELEFONO, u.EMAIL
             FROM CLIENTE c
             LEFT JOIN USUARIO u ON u.USUARIO_ID_PK = c.USUARIO_ID_FK
             WHERE c.CLIENTE_ID_PK = ? LIMIT 1'
        );
        $st->execute([$idGet]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            api_json(['ok' => false, 'error' => 'Cliente no encontrado'], 404);
        }
        $parsed = patitas_parse_cliente_apellido2($r['APELLIDO_2'] ?? null);
        $nomMedio = trim((string) $r['NOMBRE'] . ' ' . $r['APELLIDO_1'] . ' ' . $parsed['nombreCompleto']);
        api_json([
            'ok' => true,
            'cliente' => [
                'id' => (int) $r['id'],
                'nombre' => $nomMedio,
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
                'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'CL-' . str_pad((string) $r['id'], 4, '0', STR_PAD_LEFT),
            ],
        ]);
    }

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
        $parsed = patitas_parse_cliente_apellido2($r['APELLIDO_2'] ?? null);
        $nom = trim((string) $r['NOMBRE'] . ' ' . $r['APELLIDO_1'] . ' ' . $parsed['nombreCompleto']);
        $out[] = [
            'id' => (int) $r['id'],
            'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'CL-' . str_pad((string) $r['id'], 4, '0', STR_PAD_LEFT),
            'nombre' => $nom,
            'telefono' => (string) ($r['TELEFONO'] ?? ''),
            'email' => (string) ($r['EMAIL'] ?? ''),
        ];
    }

    api_json(['ok' => true, 'clientes' => $out]);
}

if ($method === 'POST') {
    api_require_login();
    api_require_staff();
    $body = api_json_body();

    $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
    $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
    $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
    $password = isset($body['password']) ? (string) $body['password'] : '';

    if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '') {
        api_json(['ok' => false, 'error' => 'Nombre, cédula, teléfono y correo son obligatorios'], 400);
    }
    if (strlen($password) < 4) {
        api_json(['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres'], 400);
    }
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        api_json(['ok' => false, 'error' => 'Correo no válido'], 400);
    }

    $st = $pdo->prepare('SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) LIMIT 1');
    $st->execute([$email]);
    if ($st->fetchColumn() !== false) {
        api_json(['ok' => false, 'error' => 'Ese correo ya está registrado'], 409);
    }

    [$nombre, $ap1, $ap2] = patitas_split_cliente_nombre($nombreCompleto, $cedula);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        api_json(['ok' => false, 'error' => 'No se pudo procesar la contraseña'], 500);
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
        api_json(['ok' => false, 'error' => 'No se pudo crear el cliente'], 500);
    }

    api_json(['ok' => true, 'clienteId' => $cid]);
}

if ($method === 'PUT') {
    api_require_login();
    api_require_staff();
    $body = api_json_body();

    $clienteId = isset($body['clienteId']) ? (int) $body['clienteId'] : 0;
    if ($clienteId <= 0) {
        api_json(['ok' => false, 'error' => 'Falta clienteId'], 400);
    }

    $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
    $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
    $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
    $password = isset($body['password']) ? (string) $body['password'] : '';

    if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '') {
        api_json(['ok' => false, 'error' => 'Datos incompletos'], 400);
    }
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        api_json(['ok' => false, 'error' => 'Correo no válido'], 400);
    }

    $st = $pdo->prepare(
        'SELECT c.USUARIO_ID_FK FROM CLIENTE c WHERE c.CLIENTE_ID_PK = ? LIMIT 1'
    );
    $st->execute([$clienteId]);
    $usuarioId = $st->fetchColumn();
    if ($usuarioId === false) {
        api_json(['ok' => false, 'error' => 'Cliente no encontrado'], 404);
    }
    $usuarioId = (int) $usuarioId;

    $st = $pdo->prepare(
        'SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) AND USUARIO_ID_PK <> ? LIMIT 1'
    );
    $st->execute([$email, $usuarioId]);
    if ($st->fetchColumn() !== false) {
        api_json(['ok' => false, 'error' => 'Ese correo pertenece a otro usuario'], 409);
    }

    [$nombre, $ap1, $ap2] = patitas_split_cliente_nombre($nombreCompleto, $cedula);

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
                api_json(['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres'], 400);
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($hash === false) {
                $pdo->rollBack();
                api_json(['ok' => false, 'error' => 'No se pudo procesar la contraseña'], 500);
            }
            $updP = $pdo->prepare('UPDATE USUARIO SET CONTRASENA = ? WHERE USUARIO_ID_PK = ?');
            $updP->execute([$hash, $usuarioId]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        api_json(['ok' => false, 'error' => 'No se pudo actualizar'], 500);
    }

    api_json(['ok' => true]);
}

api_json(['ok' => false, 'error' => 'Método no permitido'], 405);
