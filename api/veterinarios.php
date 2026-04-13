<?php
/**
 * GET: listado (sesión) o ?id= (solo staff, ficha para editar)
 * POST: crear veterinario + usuario (solo staff)
 * PUT: actualizar (solo staff)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

/**
 * @return array{0: string, 1: string, 2: string}
 */
function patitas_split_vet_nombre(string $nombreCompleto, string $cedula): array
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
 * @return array{nombreResto: string, cedulaRef: string}
 */
function patitas_parse_vet_segundo(?string $seg): array
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

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

if ($method === 'GET') {
    api_require_login();

    $idGet = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($idGet > 0) {
        api_require_staff();
        $st = $pdo->prepare(
            'SELECT v.VETERINARIO_ID_PK AS id, v.NOMBRE, v.PRIMER_APELLIDO, v.SEGUNDO_APELLIDO, v.ESPECIALIDAD, v.TELEFONO, u.EMAIL
             FROM VETERINARIO v
             JOIN USUARIO u ON u.USUARIO_ID_PK = v.USUARIO_ID_FK
             WHERE v.VETERINARIO_ID_PK = ? LIMIT 1'
        );
        $st->execute([$idGet]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            api_json(['ok' => false, 'error' => 'Veterinario no encontrado'], 404);
        }
        $parsed = patitas_parse_vet_segundo($r['SEGUNDO_APELLIDO'] ?? null);
        $nomCompleto = trim(
            (string) $r['NOMBRE'] . ' ' . $r['PRIMER_APELLIDO'] . ' ' . $parsed['nombreResto']
        );
        api_json([
            'ok' => true,
            'veterinario' => [
                'id' => (int) $r['id'],
                'nombre' => $nomCompleto,
                'especialidad' => (string) ($r['ESPECIALIDAD'] ?? ''),
                'telefono' => (string) ($r['TELEFONO'] ?? ''),
                'email' => (string) ($r['EMAIL'] ?? ''),
                'cedula' => $parsed['cedulaRef'] !== '' ? $parsed['cedulaRef'] : 'VET-' . str_pad((string) $r['id'], 3, '0', STR_PAD_LEFT),
            ],
        ]);
    }

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
        $parsed = patitas_parse_vet_segundo($r['SEGUNDO_APELLIDO'] ?? null);
        $nom = trim((string) $r['NOMBRE'] . ' ' . $r['PRIMER_APELLIDO'] . ' ' . $parsed['nombreResto']);
        $out[] = [
            'id' => (int) $r['id'],
            'nombreCompleto' => $nom,
            'especialidad' => (string) ($r['ESPECIALIDAD'] ?? ''),
            'telefono' => (string) ($r['TELEFONO'] ?? ''),
            'email' => (string) ($r['EMAIL'] ?? ''),
        ];
    }

    api_json(['ok' => true, 'veterinarios' => $out]);
}

if ($method === 'POST') {
    api_require_login();
    api_require_admin();
    $body = api_json_body();

    $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
    $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
    $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
    $especialidad = isset($body['especialidad']) ? trim((string) $body['especialidad']) : '';
    $password = isset($body['password']) ? (string) $body['password'] : '';

    if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '' || $especialidad === '') {
        api_json(['ok' => false, 'error' => 'Todos los campos obligatorios incluido especialidad'], 400);
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

    [$nombre, $ap1, $ap2] = patitas_split_vet_nombre($nombreCompleto, $cedula);

    $hash = password_hash($password, PASSWORD_DEFAULT);
    if ($hash === false) {
        api_json(['ok' => false, 'error' => 'No se pudo procesar la contraseña'], 500);
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
        api_json(['ok' => false, 'error' => 'No se pudo crear el veterinario'], 500);
    }

    api_json(['ok' => true, 'veterinarioId' => $vid]);
}

if ($method === 'PUT') {
    api_require_login();
    api_require_admin();
    $body = api_json_body();

    $vetId = isset($body['veterinarioId']) ? (int) $body['veterinarioId'] : 0;
    if ($vetId <= 0) {
        api_json(['ok' => false, 'error' => 'Falta veterinarioId'], 400);
    }

    $nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
    $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
    $telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
    $cedula = isset($body['cedula']) ? trim((string) $body['cedula']) : '';
    $especialidad = isset($body['especialidad']) ? trim((string) $body['especialidad']) : '';
    $password = isset($body['password']) ? (string) $body['password'] : '';

    if ($nombreCompleto === '' || $email === '' || $telefono === '' || $cedula === '' || $especialidad === '') {
        api_json(['ok' => false, 'error' => 'Datos incompletos'], 400);
    }
    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        api_json(['ok' => false, 'error' => 'Correo no válido'], 400);
    }

    $st = $pdo->prepare('SELECT USUARIO_ID_FK FROM VETERINARIO WHERE VETERINARIO_ID_PK = ? LIMIT 1');
    $st->execute([$vetId]);
    $usuarioId = $st->fetchColumn();
    if ($usuarioId === false) {
        api_json(['ok' => false, 'error' => 'Veterinario no encontrado'], 404);
    }
    $usuarioId = (int) $usuarioId;

    $st = $pdo->prepare(
        'SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) AND USUARIO_ID_PK <> ? LIMIT 1'
    );
    $st->execute([$email, $usuarioId]);
    if ($st->fetchColumn() !== false) {
        api_json(['ok' => false, 'error' => 'Ese correo pertenece a otro usuario'], 409);
    }

    [$nombre, $ap1, $ap2] = patitas_split_vet_nombre($nombreCompleto, $cedula);

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
