<?php
/**
 * GET: métricas para panel cliente o panel staff
 */

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

api_require_method('GET');

try {
    $pdo = Database::getConnection();
} catch (Throwable $e) {
    api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
}

api_require_login();

if (api_is_cliente()) {
    $cid = api_cliente_id($pdo);
    if ($cid === null) {
        api_json([
            'ok' => true,
            'vista' => 'cliente',
            'stats' => ['mascotas' => 0, 'citasPendientes' => 0],
            'mascotasPreview' => [],
            'citasProximas' => [],
        ]);
    }

    $st = $pdo->prepare('SELECT COUNT(*) FROM ANIMAL WHERE CLIENTE_ID_FK = ?');
    $st->execute([$cid]);
    $nMascotas = (int) $st->fetchColumn();

    $st = $pdo->prepare(
        'SELECT COUNT(*) FROM CITA c
         JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
         WHERE a.CLIENTE_ID_FK = ? AND c.ESTADO_ID_FK IN (3)'
    );
    $st->execute([$cid]);
    $nPend = (int) $st->fetchColumn();

    $sqlPrev = <<<'SQL'
SELECT a.NOMBRE AS nombre, e.ESPECIE AS especie, r.NOMBRE AS raza, a.EDAD AS edad
FROM ANIMAL a
JOIN RAZA r ON r.RAZA_ID_PK = a.RAZATB_ID_FK
JOIN ESPECIE e ON e.ESPECIE_ID_PK = r.ESPECIE_ID_FK
WHERE a.CLIENTE_ID_FK = ?
ORDER BY a.ANIMAL_ID_PK ASC
LIMIT 4
SQL;
    $st = $pdo->prepare($sqlPrev);
    $st->execute([$cid]);
    $preview = $st->fetchAll();

    $sqlCit = <<<'SQL'
SELECT c.FECHA, c.HORA_DE_INICIO, a.NOMBRE AS mascota,
       CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
WHERE a.CLIENTE_ID_FK = ? AND c.FECHA >= CURDATE()
ORDER BY c.FECHA ASC, c.HORA_DE_INICIO ASC
LIMIT 6
SQL;
    $st = $pdo->prepare($sqlCit);
    $st->execute([$cid]);
    $prox = $st->fetchAll();

    api_json([
        'ok' => true,
        'vista' => 'cliente',
        'stats' => [
            'mascotas' => $nMascotas,
            'citasPendientes' => $nPend,
        ],
        'mascotasPreview' => $preview,
        'citasProximas' => $prox,
    ]);
}

$nClientes = (int) $pdo->query('SELECT COUNT(*) FROM CLIENTE')->fetchColumn();
$nVets = (int) $pdo->query('SELECT COUNT(*) FROM VETERINARIO')->fetchColumn();
$nAnimales = (int) $pdo->query('SELECT COUNT(*) FROM ANIMAL')->fetchColumn();
$nInv = (int) $pdo->query('SELECT COUNT(*) FROM MEDICAMENTO')->fetchColumn();

$st = $pdo->query(
    "SELECT COUNT(*) FROM CITA WHERE FECHA = CURDATE()"
);
$citasHoy = (int) $st->fetchColumn();

$st = $pdo->query(
    "SELECT COUNT(*) FROM CITA WHERE ESTADO_ID_FK = 3"
);
$citasPend = (int) $st->fetchColumn();

$st = $pdo->query(
    "SELECT COALESCE(SUM(MONTO_TOTAL), 0) FROM PAGO
     WHERE MONTH(FECHA_DE_PAGO) = MONTH(CURDATE()) AND YEAR(FECHA_DE_PAGO) = YEAR(CURDATE())"
);
$ingresos = (float) $st->fetchColumn();

$sqlHoy = <<<'SQL'
SELECT c.HORA_DE_INICIO, a.NOMBRE AS mascota, r.NOMBRE AS raza,
       CONCAT(cl.NOMBRE, ' ', cl.APELLIDO_1) AS dueno,
       CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
       es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN RAZA r ON r.RAZA_ID_PK = a.RAZATB_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
WHERE c.FECHA = CURDATE()
ORDER BY c.HORA_DE_INICIO ASC
LIMIT 12
SQL;
$citasHoyList = $pdo->query($sqlHoy)->fetchAll();

$sqlUr = <<<'SQL'
SELECT u.EMAIL AS email, CONCAT(c.NOMBRE, ' ', c.APELLIDO_1) AS nombre
FROM USUARIO u
JOIN CLIENTE c ON c.USUARIO_ID_FK = u.USUARIO_ID_PK
WHERE u.ROL_FK = 3
ORDER BY u.USUARIO_ID_PK DESC
LIMIT 8
SQL;
$usuariosRecientes = $pdo->query($sqlUr)->fetchAll();

api_json([
    'ok' => true,
    'vista' => 'staff',
    'stats' => [
        'usuarios' => $nClientes,
        'veterinarios' => $nVets,
        'animales' => $nAnimales,
        'citasHoy' => $citasHoy,
        'citasPendientes' => $citasPend,
        'inventarioItems' => $nInv,
        'ingresosMes' => $ingresos,
    ],
    'citasHoyList' => $citasHoyList,
    'usuariosRecientes' => $usuariosRecientes,
]);
