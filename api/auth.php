<?php
/**
 * POST: login (JSON email, password)
 * GET: sesión actual (si existe)
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    if (! empty($_SESSION['usuario_id'])) {
        api_json([
            'ok' => true,
            'usuario' => [
                'usuarioId' => (int) $_SESSION['usuario_id'],
                'email' => (string) $_SESSION['usuario_email'],
                'nombre' => (string) $_SESSION['usuario_nombre'],
                'rol' => (string) $_SESSION['usuario_rol'],
                'rolFk' => isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0,
            ],
        ]);
    }
    api_json(['ok' => false, 'usuario' => null]);
}

api_require_method('POST');

$body = api_json_body();
$email = isset($body['email']) ? trim((string) $body['email']) : '';
$password = isset($body['password']) ? (string) $body['password'] : '';

if ($email === '' || $password === '') {
    api_json(['ok' => false, 'error' => 'Correo y contraseña son obligatorios'], 400);
}

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos', 'detalle' => getenv('APP_DEBUG') ? $e->getMessage() : null], 503);
}

$sql = <<<'SQL'
SELECT
    u.USUARIO_ID_PK,
    u.EMAIL,
    u.ROL_FK,
    u.CONTRASENA,
    COALESCE(
        CONCAT(TRIM(c.NOMBRE), ' ', TRIM(c.APELLIDO_1)),
        CONCAT(TRIM(v.NOMBRE), ' ', TRIM(v.PRIMER_APELLIDO)),
        CASE WHEN u.ROL_FK = 1 THEN 'Administrador' ELSE 'Usuario' END
    ) AS NOMBRE_DISPLAY
FROM USUARIO u
LEFT JOIN CLIENTE c ON c.USUARIO_ID_FK = u.USUARIO_ID_PK
LEFT JOIN VETERINARIO v ON v.USUARIO_ID_FK = u.USUARIO_ID_PK
WHERE LOWER(u.EMAIL) = LOWER(:email) AND u.ESTADO_ID_FK = 1
LIMIT 1
SQL;

$st = $pdo->prepare($sql);
$st->execute(['email' => $email]);
$row = $st->fetch();

if (! $row) {
    api_json(['ok' => false, 'error' => 'Correo o contraseña incorrectos'], 401);
}

$hash = (string) $row['CONTRASENA'];
$valido = false;
if (str_starts_with($hash, '$2')) {
    $valido = password_verify($password, $hash);
} else {
    $valido = hash_equals($hash, $password);
}

if (! $valido) {
    api_json(['ok' => false, 'error' => 'Correo o contraseña incorrectos'], 401);
}

$rolFk = (int) $row['ROL_FK'];
/** UI: cliente (3) vs panel admin (1 administrador, 2 veterinario) */
$rolUi = $rolFk === 3 ? 'cliente' : 'admin';
$nombre = trim((string) $row['NOMBRE_DISPLAY']);

$_SESSION['usuario_id'] = (int) $row['USUARIO_ID_PK'];
$_SESSION['usuario_email'] = (string) $row['EMAIL'];
$_SESSION['usuario_nombre'] = $nombre !== '' ? $nombre : 'Usuario';
$_SESSION['usuario_rol'] = $rolUi;
$_SESSION['rol_fk'] = $rolFk;

api_json([
    'ok' => true,
    'usuario' => [
        'usuarioId' => (int) $row['USUARIO_ID_PK'],
        'email' => (string) $row['EMAIL'],
        'nombre' => $_SESSION['usuario_nombre'],
        'rol' => $rolUi,
        'rolFk' => $rolFk,
    ],
]);
