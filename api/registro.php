<?php
/**
 * POST: registro de cliente (USUARIO + CLIENTE). JSON: nombre, email, password, telefono, cedula (opcional).
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

api_require_method('POST');

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

$body = api_json_body();
$email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
$password = isset($body['password']) ? (string) $body['password'] : '';
$nombreCompleto = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
$telefono = isset($body['telefono']) ? trim((string) $body['telefono']) : '';

if ($email === '' || $password === '' || $nombreCompleto === '' || $telefono === '') {
    api_json(['ok' => false, 'error' => 'Nombre, correo, teléfono y contraseña son obligatorios'], 400);
}

if (strlen($password) < 4) {
    api_json(['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres'], 400);
}

if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_json(['ok' => false, 'error' => 'Correo electrónico no válido'], 400);
}

$st = $pdo->prepare('SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) LIMIT 1');
$st->execute([$email]);
if ($st->fetchColumn() !== false) {
    api_json(['ok' => false, 'error' => 'Ese correo ya está registrado'], 409);
}

$partes = preg_split('/\s+/', $nombreCompleto, -1, PREG_SPLIT_NO_EMPTY) ?: [];
if (count($partes) === 1) {
    $nombre = $partes[0];
    $ap1 = '.';
    $ap2 = null;
} else {
    $nombre = array_shift($partes);
    $ap1 = array_shift($partes) ?? '.';
    $ap2 = count($partes) > 0 ? implode(' ', $partes) : null;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
if ($hash === false) {
    api_json(['ok' => false, 'error' => 'No se pudo procesar la contraseña'], 500);
}

$direccionDefault = 1;

$pdo->beginTransaction();
try {
    $st = $pdo->query('SELECT COALESCE(MAX(USUARIO_ID_PK), 0) + 1 FROM USUARIO');
    $uid = (int) $st->fetchColumn();

    $st = $pdo->query('SELECT COALESCE(MAX(CLIENTE_ID_PK), 0) + 1 FROM CLIENTE');
    $cid = (int) $st->fetchColumn();

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
    api_json(['ok' => false, 'error' => 'No se pudo completar el registro'], 500);
}

api_json(['ok' => true, 'mensaje' => 'Cuenta creada. Ya puedes iniciar sesión.']);
