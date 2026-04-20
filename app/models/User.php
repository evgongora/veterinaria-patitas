<?php

declare(strict_types=1);

/**
 * Modelo USUARIO — autenticación y registro público de cliente.
 */
final class User
{
    public static function sessionPayloadFromGlobals(): ?array
    {
        if (empty($_SESSION['usuario_id'])) {
            return null;
        }

        return [
            'usuarioId' => (int) $_SESSION['usuario_id'],
            'email' => (string) $_SESSION['usuario_email'],
            'nombre' => (string) $_SESSION['usuario_nombre'],
            'rol' => (string) $_SESSION['usuario_rol'],
            'rolFk' => isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0,
        ];
    }


    public static function login(PDO $pdo, string $email, string $password): array
    {
        if ($email === '' || $password === '') {
            return ['ok' => false, 'error' => 'Correo y contraseña son obligatorios', 'code' => 400];
        }
        if (strlen($email) > 254 || strlen($password) > 200) {
            return ['ok' => false, 'error' => 'Correo o contraseña incorrectos', 'code' => 401];
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Correo o contraseña incorrectos', 'code' => 401];
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
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            return ['ok' => false, 'error' => 'Correo o contraseña incorrectos', 'code' => 401];
        }

        $hash = (string) $row['CONTRASENA'];
        $valido = str_starts_with($hash, '$2')
            ? password_verify($password, $hash)
            : hash_equals($hash, $password);

        if (! $valido) {
            return ['ok' => false, 'error' => 'Correo o contraseña incorrectos', 'code' => 401];
        }

        $rolFk = (int) $row['ROL_FK'];
        $rolUi = $rolFk === 3 ? 'cliente' : 'admin';
        $nombre = trim((string) $row['NOMBRE_DISPLAY']);
        $nombre = $nombre !== '' ? $nombre : 'Usuario';

        $_SESSION['usuario_id'] = (int) $row['USUARIO_ID_PK'];
        $_SESSION['usuario_email'] = (string) $row['EMAIL'];
        $_SESSION['usuario_nombre'] = $nombre;
        $_SESSION['usuario_rol'] = $rolUi;
        $_SESSION['rol_fk'] = $rolFk;

        return [
            'ok' => true,
            'usuario' => [
                'usuarioId' => (int) $row['USUARIO_ID_PK'],
                'email' => (string) $row['EMAIL'],
                'nombre' => $nombre,
                'rol' => $rolUi,
                'rolFk' => $rolFk,
            ],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], (bool) $p['secure'], (bool) $p['httponly']);
        }
        session_destroy();
    }


    public static function registrarCliente(PDO $pdo, array $body): array
    {
        $email = isset($body['email']) ? trim(mb_strtolower((string) $body['email'])) : '';
        $password = isset($body['password']) ? (string) $body['password'] : '';
        $nombreCompleto = isset($body['nombre']) ? preg_replace('/\s+/u', ' ', trim((string) $body['nombre'])) : '';
        $telefonoRaw = isset($body['telefono']) ? trim((string) $body['telefono']) : '';
        $cedulaDigits = isset($body['cedula']) ? preg_replace('/\D+/', '', (string) $body['cedula']) : '';

        if ($email === '' || $password === '' || $nombreCompleto === '' || $telefonoRaw === '') {
            return ['ok' => false, 'error' => 'Nombre, correo, teléfono y contraseña son obligatorios', 'code' => 400];
        }
        if (mb_strlen($nombreCompleto) > 150) {
            return ['ok' => false, 'error' => 'El nombre completo es demasiado largo', 'code' => 400];
        }
        if (strlen($password) < 4 || strlen($password) > 200) {
            return ['ok' => false, 'error' => 'La contraseña debe tener entre 4 y 200 caracteres', 'code' => 400];
        }
        if (strlen($email) > 254 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Correo electrónico no válido', 'code' => 400];
        }

        if ($cedulaDigits === '' || strlen($cedulaDigits) < 9 || strlen($cedulaDigits) > 10) {
            return ['ok' => false, 'error' => 'Cédula no válida (9 o 10 dígitos)', 'code' => 400];
        }

        $telefonoDigits = preg_replace('/\D+/', '', $telefonoRaw);
        if (strlen($telefonoDigits) >= 11 && str_starts_with($telefonoDigits, '506')) {
            $telefonoDigits = substr($telefonoDigits, 3);
        }
        if (strlen($telefonoDigits) < 8 || strlen($telefonoDigits) > 12) {
            return ['ok' => false, 'error' => 'Teléfono no válido', 'code' => 400];
        }
        $telefono = $telefonoDigits;

        $st = $pdo->prepare('SELECT 1 FROM USUARIO WHERE LOWER(EMAIL) = LOWER(?) LIMIT 1');
        $st->execute([$email]);
        if ($st->fetchColumn() !== false) {
            return ['ok' => false, 'error' => 'Ese correo ya está registrado', 'code' => 409];
        }

        $partes = preg_split('/\s+/', $nombreCompleto, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (count($partes) < 2) {
            return ['ok' => false, 'error' => 'Indica nombre y al menos un apellido', 'code' => 400];
        }
        foreach ($partes as $p) {
            if (mb_strlen($p) > 50) {
                return ['ok' => false, 'error' => 'Cada parte del nombre no puede superar 50 caracteres', 'code' => 400];
            }
            $token = (mb_strlen($p) > 1 && str_ends_with($p, '.')) ? mb_substr($p, 0, -1) : $p;
            if (mb_strlen($token) < 2) {
                return ['ok' => false, 'error' => 'Nombre o apellido demasiado corto', 'code' => 400];
            }
            if (! preg_match('/^[\p{L}]+(?:[\'.\-][\p{L}]+)*\.?$/u', $p)) {
                return ['ok' => false, 'error' => 'El nombre solo puede contener letras y separadores habituales', 'code' => 400];
            }
        }

        $nombre = array_shift($partes);
        $ap1 = array_shift($partes) ?? '.';
        $ap2 = count($partes) > 0 ? implode(' ', $partes) : null;
        if ($ap2 !== null && mb_strlen($ap2) > 50) {
            return ['ok' => false, 'error' => 'El segundo apellido o apellidos compuestos no pueden superar 50 caracteres', 'code' => 400];
        }

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

            return ['ok' => false, 'error' => 'No se pudo completar el registro', 'code' => 500];
        }

        return ['ok' => true, 'mensaje' => 'Cuenta creada. Ya puedes iniciar sesión.'];
    }
}
