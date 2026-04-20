<?php

declare(strict_types=1);

/**
 * Modelo agregado — métricas de panel cliente y staff.
 */
final class Dashboard
{

    public static function datosCliente(PDO $pdo, ?int $cid): array
    {
        if ($cid === null) {
            return [
                'ok' => true,
                'vista' => 'cliente',
                'stats' => ['mascotas' => 0, 'citasPendientes' => 0],
                'mascotasPreview' => [],
                'citasProximas' => [],
            ];
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
SELECT
    c.CITA_ID_PK,
    c.FECHA,
    c.HORA_DE_INICIO,
    a.NOMBRE AS mascota,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    t.DESCRIPCION AS tipo,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
WHERE a.CLIENTE_ID_FK = ?
  AND c.ESTADO_ID_FK NOT IN (4, 5)
  AND c.FECHA >= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
ORDER BY c.FECHA ASC, c.HORA_DE_INICIO ASC
LIMIT 8
SQL;
        $st = $pdo->prepare($sqlCit);
        $st->execute([$cid]);
        $prox = [];
        foreach ($st->fetchAll() as $r) {
            $hi = substr((string) $r['HORA_DE_INICIO'], 0, 5);
            $prox[] = [
                'id' => (int) $r['CITA_ID_PK'],
                'fecha' => (string) $r['FECHA'],
                'horaInicio' => $hi,
                'mascota' => (string) $r['mascota'],
                'veterinario' => (string) $r['veterinario'],
                'tipo' => (string) $r['tipo'],
                'estado' => (string) $r['estado'],
            ];
        }

        return [
            'ok' => true,
            'vista' => 'cliente',
            'stats' => [
                'mascotas' => $nMascotas,
                'citasPendientes' => $nPend,
            ],
            'mascotasPreview' => $preview,
            'citasProximas' => $prox,
        ];
    }


    public static function datosStaff(PDO $pdo): array
    {
        $hoy = patitas_fecha_hoy_ymd();

        $nClientes = (int) $pdo->query('SELECT COUNT(*) FROM CLIENTE')->fetchColumn();
        $nVets = (int) $pdo->query('SELECT COUNT(*) FROM VETERINARIO')->fetchColumn();
        $nAnimales = (int) $pdo->query('SELECT COUNT(*) FROM ANIMAL')->fetchColumn();
        $nInv = (int) $pdo->query('SELECT COUNT(*) FROM MEDICAMENTO')->fetchColumn();

        $st = $pdo->prepare('SELECT COUNT(*) FROM CITA WHERE DATE(FECHA) = ?');
        $st->execute([$hoy]);
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
WHERE DATE(c.FECHA) = ?
ORDER BY c.HORA_DE_INICIO ASC
LIMIT 12
SQL;
        $stHoy = $pdo->prepare($sqlHoy);
        $stHoy->execute([$hoy]);
        $citasHoyList = $stHoy->fetchAll();

        $sqlUr = <<<'SQL'
SELECT u.EMAIL AS email, CONCAT(c.NOMBRE, ' ', c.APELLIDO_1) AS nombre
FROM USUARIO u
JOIN CLIENTE c ON c.USUARIO_ID_FK = u.USUARIO_ID_PK
WHERE u.ROL_FK = 3
ORDER BY u.USUARIO_ID_PK DESC
LIMIT 8
SQL;
        $usuariosRecientes = $pdo->query($sqlUr)->fetchAll();

        return [
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
        ];
    }
}
