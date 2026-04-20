<?php

declare(strict_types=1);

/**
 * Modelo CITA — listados y creación cliente.
 */
final class Cita
{

    public static function listarParaUsuario(PDO $pdo, bool $esCliente, ?int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    DATE_FORMAT(c.FECHA, '%Y-%m-%d') AS fecha,
    c.HORA_DE_INICIO,
    c.HORA_DE_FINALIZACION,
    c.ESTADO_ID_FK AS estadoId,
    a.NOMBRE AS animal,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    t.DESCRIPCION AS tipo,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
SQL;

        if ($esCliente) {
            if ($clienteId === null) {
                return [];
            }
            $sql .= ' WHERE a.CLIENTE_ID_FK = ? ORDER BY c.FECHA DESC, c.HORA_DE_INICIO DESC';
            $st = $pdo->prepare($sql);
            $st->execute([$clienteId]);
        } else {
            $sql .= ' ORDER BY c.FECHA DESC, c.HORA_DE_INICIO DESC';
            $st = $pdo->query($sql);
        }

        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[] = self::filaListadoCita($r);
        }

        return $out;
    }

    /**
     * Citas del día calendario en zona clínica (Costa Rica), no CURDATE() del servidor MySQL.
     */
    public static function listarCitasHoy(PDO $pdo, bool $esCliente, ?int $clienteId): array
    {
        $hoy = patitas_fecha_hoy_ymd();

        $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    DATE_FORMAT(c.FECHA, '%Y-%m-%d') AS fecha,
    c.HORA_DE_INICIO,
    c.HORA_DE_FINALIZACION,
    c.ESTADO_ID_FK AS estadoId,
    a.NOMBRE AS animal,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    t.DESCRIPCION AS tipo,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
WHERE DATE(c.FECHA) = ?
SQL;
        if ($esCliente) {
            if ($clienteId === null) {
                return [];
            }
            $sql .= ' AND a.CLIENTE_ID_FK = ? ORDER BY c.HORA_DE_INICIO ASC';
            $st = $pdo->prepare($sql);
            $st->execute([$hoy, $clienteId]);
        } else {
            $sql .= ' ORDER BY c.HORA_DE_INICIO ASC';
            $st = $pdo->prepare($sql);
            $st->execute([$hoy]);
        }

        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[] = self::filaListadoCita($r);
        }

        return $out;
    }

    private static function filaListadoCita(array $r): array
    {
        $pk = $r['CITA_ID_PK'] ?? $r['cita_id_pk'] ?? 0;
        $fecha = (string) ($r['fecha'] ?? $r['FECHA'] ?? '');
        if (strlen($fecha) > 10) {
            $fecha = substr($fecha, 0, 10);
        }
        $hiRaw = $r['HORA_DE_INICIO'] ?? $r['hora_de_inicio'] ?? '';
        $hi = substr((string) $hiRaw, 0, 5);
        $hfRaw = $r['HORA_DE_FINALIZACION'] ?? $r['hora_de_finalizacion'] ?? null;
        $hf = $hfRaw ? substr((string) $hfRaw, 0, 5) : $hi;

        return [
            'id' => (int) $pk,
            'animal' => (string) ($r['animal'] ?? ''),
            'veterinario' => (string) ($r['veterinario'] ?? ''),
            'fecha' => $fecha,
            'horaInicio' => $hi,
            'horaFin' => $hf,
            'tipo' => (string) ($r['tipo'] ?? ''),
            'estadoId' => (int) ($r['estadoId'] ?? $r['estadoid'] ?? 0),
            'estado' => (string) ($r['estado'] ?? ''),
        ];
    }


    public static function crearCliente(PDO $pdo, int $clienteId, array $body): array
    {
        $animalId = isset($body['animalId']) ? (int) $body['animalId'] : 0;
        $vetId = isset($body['veterinarioId']) ? (int) $body['veterinarioId'] : 0;
        $fecha = isset($body['fecha']) ? trim((string) $body['fecha']) : '';
        $horaInicio = isset($body['horaInicio']) ? trim((string) $body['horaInicio']) : '';
        $tipoCitaId = isset($body['tipoCitaId']) ? (int) $body['tipoCitaId'] : 1;
        $notas = isset($body['notas']) ? trim((string) $body['notas']) : '';

        if ($animalId <= 0 || $vetId <= 0 || $fecha === '' || $horaInicio === '') {
            return ['ok' => false, 'error' => 'Faltan datos obligatorios', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT CLIENTE_ID_FK FROM ANIMAL WHERE ANIMAL_ID_PK = ? LIMIT 1');
        $st->execute([$animalId]);
        $owner = $st->fetchColumn();
        if ($owner === false || (int) $owner !== $clienteId) {
            return ['ok' => false, 'error' => 'El animal no pertenece a tu cuenta', 'code' => 403];
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return ['ok' => false, 'error' => 'Fecha inválida (use YYYY-MM-DD)', 'code' => 400];
        }

        if (! preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
            return ['ok' => false, 'error' => 'Hora inválida', 'code' => 400];
        }

        $ts = strtotime($fecha . ' ' . $horaInicio . ':00');
        if ($ts === false) {
            return ['ok' => false, 'error' => 'Fecha u hora no válidas', 'code' => 400];
        }

        $horaFin = date('H:i:s', strtotime('+30 minutes', $ts));

        if (self::hayConflictoVeterinario($pdo, $vetId, $fecha, $horaInicio . ':00', $horaFin, null)) {
            return [
                'ok' => false,
                'error' => 'Ese horario ya está reservado con el veterinario elegido. Elige otra hora u otro día.',
                'code' => 409,
            ];
        }

        $st = $pdo->query('SELECT COALESCE(MAX(CITA_ID_PK), 0) + 1 AS n FROM CITA');
        $nextId = (int) $st->fetchColumn();

        $ins = $pdo->prepare(
            'INSERT INTO CITA (CITA_ID_PK, ANIMAL_ID_FK, ESTADO_ID_FK, VETERINARIO_ID_FK, TIPO_DE_CITA_ID_FK, FECHA, HORA_DE_INICIO, HORA_DE_FINALIZACION, NOTAS_VETERINARIO)
             VALUES (?, ?, 3, ?, ?, ?, ?, ?, ?)'
        );
        $ins->execute([
            $nextId,
            $animalId,
            $vetId,
            $tipoCitaId,
            $fecha,
            $horaInicio . ':00',
            $horaFin,
            $notas !== '' ? $notas : null,
        ]);

        return ['ok' => true, 'citaId' => $nextId];
    }

    /**
     * Detalle de una cita para edición cliente (solo si el animal es suyo).
     */
    public static function obtenerParaCliente(PDO $pdo, int $citaId, int $clienteId): array
    {
        if ($citaId <= 0) {
            return ['ok' => false, 'error' => 'Falta identificador de cita', 'code' => 400];
        }

        $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    c.ANIMAL_ID_FK,
    DATE_FORMAT(c.FECHA, '%Y-%m-%d') AS fecha,
    c.HORA_DE_INICIO,
    c.VETERINARIO_ID_FK,
    c.TIPO_DE_CITA_ID_FK,
    c.NOTAS_VETERINARIO,
    c.ESTADO_ID_FK,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
WHERE c.CITA_ID_PK = ? AND a.CLIENTE_ID_FK = ?
LIMIT 1
SQL;
        $st = $pdo->prepare($sql);
        $st->execute([$citaId, $clienteId]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        if (! $r) {
            return ['ok' => false, 'error' => 'Cita no encontrada', 'code' => 404];
        }

        $hi = substr((string) $r['HORA_DE_INICIO'], 0, 5);

        return [
            'ok' => true,
            'cita' => [
                'id' => (int) $r['CITA_ID_PK'],
                'animalId' => (int) $r['ANIMAL_ID_FK'],
                'veterinarioId' => (int) $r['VETERINARIO_ID_FK'],
                'tipoCitaId' => (int) $r['TIPO_DE_CITA_ID_FK'],
                'fecha' => (string) ($r['fecha'] ?? ''),
                'horaInicio' => $hi,
                'notas' => (string) ($r['NOTAS_VETERINARIO'] ?? ''),
                'estadoId' => (int) $r['ESTADO_ID_FK'],
                'estado' => (string) $r['estado'],
            ],
        ];
    }

    /**
     * Actualización por cliente: solo citas en estado Pendiente (3).
     */
    public static function actualizarCliente(PDO $pdo, int $clienteId, array $body): array
    {
        $citaId = isset($body['citaId']) ? (int) $body['citaId'] : 0;
        $animalId = isset($body['animalId']) ? (int) $body['animalId'] : 0;
        $vetId = isset($body['veterinarioId']) ? (int) $body['veterinarioId'] : 0;
        $fecha = isset($body['fecha']) ? trim((string) $body['fecha']) : '';
        $horaInicio = isset($body['horaInicio']) ? trim((string) $body['horaInicio']) : '';
        $tipoCitaId = isset($body['tipoCitaId']) ? (int) $body['tipoCitaId'] : 1;
        $notas = isset($body['notas']) ? trim((string) $body['notas']) : '';

        if ($citaId <= 0) {
            return ['ok' => false, 'error' => 'Falta citaId', 'code' => 400];
        }
        if ($animalId <= 0 || $vetId <= 0 || $fecha === '' || $horaInicio === '') {
            return ['ok' => false, 'error' => 'Faltan datos obligatorios', 'code' => 400];
        }

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return ['ok' => false, 'error' => 'Fecha inválida (use YYYY-MM-DD)', 'code' => 400];
        }

        if (! preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
            return ['ok' => false, 'error' => 'Hora inválida', 'code' => 400];
        }

        $st = $pdo->prepare(
            'SELECT c.ESTADO_ID_FK FROM CITA c
             INNER JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
             WHERE c.CITA_ID_PK = ? AND a.CLIENTE_ID_FK = ? LIMIT 1'
        );
        $st->execute([$citaId, $clienteId]);
        $estadoFk = $st->fetchColumn();
        if ($estadoFk === false) {
            return ['ok' => false, 'error' => 'Cita no encontrada', 'code' => 404];
        }
        if ((int) $estadoFk !== 3) {
            return [
                'ok' => false,
                'error' => 'Solo puedes editar citas pendientes. Para otros cambios, contacta a la clínica.',
                'code' => 403,
            ];
        }

        $st = $pdo->prepare('SELECT CLIENTE_ID_FK FROM ANIMAL WHERE ANIMAL_ID_PK = ? LIMIT 1');
        $st->execute([$animalId]);
        $owner = $st->fetchColumn();
        if ($owner === false || (int) $owner !== $clienteId) {
            return ['ok' => false, 'error' => 'El animal no pertenece a tu cuenta', 'code' => 403];
        }

        $ts = strtotime($fecha . ' ' . $horaInicio . ':00');
        if ($ts === false) {
            return ['ok' => false, 'error' => 'Fecha u hora no válidas', 'code' => 400];
        }

        $horaFin = date('H:i:s', strtotime('+30 minutes', $ts));

        if (self::hayConflictoVeterinario($pdo, $vetId, $fecha, $horaInicio . ':00', $horaFin, $citaId)) {
            return [
                'ok' => false,
                'error' => 'Ese horario ya está reservado con el veterinario elegido. Elige otra hora u otro día.',
                'code' => 409,
            ];
        }

        $upd = $pdo->prepare(
            'UPDATE CITA SET ANIMAL_ID_FK = ?, VETERINARIO_ID_FK = ?, TIPO_DE_CITA_ID_FK = ?, FECHA = ?, HORA_DE_INICIO = ?, HORA_DE_FINALIZACION = ?, NOTAS_VETERINARIO = ?
             WHERE CITA_ID_PK = ?'
        );
        $upd->execute([
            $animalId,
            $vetId,
            $tipoCitaId,
            $fecha,
            $horaInicio . ':00',
            $horaFin,
            $notas !== '' ? $notas : null,
            $citaId,
        ]);

        return ['ok' => true];
    }

    /**
     * Horarios del formulario de reserva (debe coincidir con la vista).
     */
    public static function horariosGridReserva(): array
    {
        return ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00'];
    }

    /**
     * Inicios de franja (HH:MM) que chocan con citas activas del veterinario en esa fecha.
     */
    public static function horasOcupadasParaReserva(PDO $pdo, int $vetId, string $fechaYmd, ?int $exceptCitaId = null): array
    {
        $sql = <<<'SQL'
SELECT HORA_DE_INICIO, HORA_DE_FINALIZACION FROM CITA
WHERE VETERINARIO_ID_FK = ? AND FECHA = ? AND ESTADO_ID_FK NOT IN (4, 5)
SQL;
        $bind = [$vetId, $fechaYmd];
        if ($exceptCitaId !== null && $exceptCitaId > 0) {
            $sql .= ' AND CITA_ID_PK <> ?';
            $bind[] = $exceptCitaId;
        }
        $st = $pdo->prepare($sql);
        $st->execute($bind);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        $blocked = [];
        foreach (self::horariosGridReserva() as $slot) {
            $s0 = strtotime($fechaYmd . ' ' . $slot . ':00');
            $s1 = strtotime($fechaYmd . ' ' . $slot . ':00 +30 minutes');
            if ($s0 === false || $s1 === false) {
                continue;
            }
            foreach ($rows as $r) {
                $a = substr((string) $r['HORA_DE_INICIO'], 0, 8);
                $bRaw = $r['HORA_DE_FINALIZACION'] ?? null;
                $t0 = strtotime($fechaYmd . ' ' . $a);
                if ($t0 === false) {
                    continue;
                }
                if ($bRaw !== null && (string) $bRaw !== '') {
                    $t1 = strtotime($fechaYmd . ' ' . substr((string) $bRaw, 0, 8));
                } else {
                    $t1 = strtotime($fechaYmd . ' ' . $a . ' +30 minutes');
                }
                if ($t1 === false) {
                    continue;
                }
                if (! ($t1 <= $s0 || $t0 >= $s1)) {
                    $blocked[] = $slot;
                    break;
                }
            }
        }

        return array_values(array_unique($blocked));
    }

    /**
     * Solape con otra cita del mismo veterinario (no completada ni cancelada).
     */
    public static function hayConflictoVeterinario(
        PDO $pdo,
        int $vetId,
        string $fechaYmd,
        string $horaInicioHhMmSs,
        string $horaFinHhMmSs,
        ?int $exceptCitaId
    ): bool {
        $ini = strlen($horaInicioHhMmSs) === 5 ? $horaInicioHhMmSs . ':00' : $horaInicioHhMmSs;
        $fin = strlen($horaFinHhMmSs) === 5 ? $horaFinHhMmSs . ':00' : $horaFinHhMmSs;

        $sql = <<<'SQL'
SELECT 1 FROM CITA c
WHERE c.VETERINARIO_ID_FK = ? AND c.FECHA = ? AND c.ESTADO_ID_FK NOT IN (4, 5)
SQL;
        $bind = [$vetId, $fechaYmd];
        if ($exceptCitaId !== null && $exceptCitaId > 0) {
            $sql .= ' AND c.CITA_ID_PK <> ?';
            $bind[] = $exceptCitaId;
        }
        $sql .= <<<'SQL'
 AND NOT (
    ? <= TIME(c.HORA_DE_INICIO)
    OR ? >= TIME(COALESCE(c.HORA_DE_FINALIZACION, ADDTIME(c.HORA_DE_INICIO, '0:30:00')))
)
LIMIT 1
SQL;
        $bind[] = $fin;
        $bind[] = $ini;
        $st = $pdo->prepare($sql);
        $st->execute($bind);

        return (bool) $st->fetchColumn();
    }

    /**
     * Citas pendientes de aprobación (estado 3). Si $soloVeterinarioId no es null, solo esa agenda.
     */
    public static function listarPendientesAprobacion(PDO $pdo, ?int $soloVeterinarioId): array
    {
        $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    DATE_FORMAT(c.FECHA, '%Y-%m-%d') AS fecha,
    c.HORA_DE_INICIO,
    a.NOMBRE AS animal,
    CONCAT(cl.NOMBRE, ' ', cl.APELLIDO_1) AS cliente,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    c.VETERINARIO_ID_FK AS veterinarioId,
    t.DESCRIPCION AS tipo,
    es.DESCRIPCION AS estado
FROM CITA c
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
JOIN ESTADO es ON es.ESTADO_ID_PK = c.ESTADO_ID_FK
WHERE c.ESTADO_ID_FK = 3
SQL;
        if ($soloVeterinarioId !== null && $soloVeterinarioId > 0) {
            $sql .= ' AND c.VETERINARIO_ID_FK = ?';
        }
        $sql .= ' ORDER BY c.FECHA ASC, c.HORA_DE_INICIO ASC';
        $st = $pdo->prepare($sql);
        if ($soloVeterinarioId !== null && $soloVeterinarioId > 0) {
            $st->execute([$soloVeterinarioId]);
        } else {
            $st->execute();
        }
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $hi = substr((string) $r['HORA_DE_INICIO'], 0, 5);
            $out[] = [
                'id' => (int) $r['CITA_ID_PK'],
                'fecha' => (string) ($r['fecha'] ?? ''),
                'horaInicio' => $hi,
                'animal' => (string) $r['animal'],
                'cliente' => (string) $r['cliente'],
                'veterinario' => (string) $r['veterinario'],
                'veterinarioId' => (int) $r['veterinarioId'],
                'tipo' => (string) $r['tipo'],
                'estado' => (string) $r['estado'],
            ];
        }

        return $out;
    }

    /**
     * Citas donde aún se puede registrar un pago (staff). Excluye pendientes de aprobación, canceladas y ya cobradas.
     */
    public static function listarParaCobro(PDO $pdo, bool $esAdmin, ?int $vetIdStaff): array
    {
        $sql = <<<'SQL'
SELECT
    c.CITA_ID_PK,
    DATE_FORMAT(c.FECHA, '%Y-%m-%d') AS fecha,
    c.HORA_DE_INICIO,
    a.NOMBRE AS animal,
    CONCAT(cl.NOMBRE, ' ', cl.APELLIDO_1) AS cliente,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario,
    f.FACTURA_ID_PK,
    f.TOTAL_A_COBRAR,
    COALESCE((
        SELECT SUM(s.PRECIO) FROM SERVICIOS_POR_CITA sp
        INNER JOIN SERVICIO s ON s.SERVICIO_ID_PK = sp.SERVICIO_ID_FK
        WHERE sp.CITA_ID_FK = c.CITA_ID_PK
    ), 0) AS total_servicios
FROM CITA c
INNER JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
INNER JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
INNER JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
LEFT JOIN FACTURA f ON f.CITA_ID_FK = c.CITA_ID_PK
WHERE c.ESTADO_ID_FK NOT IN (3, 5)
AND NOT EXISTS (
    SELECT 1 FROM PAGO p
    INNER JOIN FACTURA fx ON fx.FACTURA_ID_PK = p.FACTURA_ID_FK
    WHERE fx.CITA_ID_FK = c.CITA_ID_PK AND p.ESTADO_ID_FK = 7
)
AND (
    f.FACTURA_ID_PK IS NULL
    OR f.ESTADO_ID_FK <> 7
)
SQL;
        if (! $esAdmin) {
            if ($vetIdStaff === null || $vetIdStaff <= 0) {
                return [];
            }
            $sql .= ' AND c.VETERINARIO_ID_FK = ?';
        }
        $sql .= ' ORDER BY c.FECHA DESC, c.HORA_DE_INICIO DESC';
        $st = $pdo->prepare($sql);
        if (! $esAdmin) {
            $st->execute([$vetIdStaff]);
        } else {
            $st->execute();
        }
        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $fid = $r['FACTURA_ID_PK'] !== null && $r['FACTURA_ID_PK'] !== '' ? (int) $r['FACTURA_ID_PK'] : 0;
            $totalFactura = $fid > 0 ? (float) $r['TOTAL_A_COBRAR'] : 0.0;
            $totalServ = (float) $r['total_servicios'];
            $totalSugerido = $fid > 0 ? $totalFactura : ($totalServ > 0 ? $totalServ : 0.0);
            $hi = substr((string) $r['HORA_DE_INICIO'], 0, 5);
            $out[] = [
                'citaId' => (int) $r['CITA_ID_PK'],
                'fecha' => (string) $r['fecha'],
                'horaInicio' => $hi,
                'animal' => (string) $r['animal'],
                'cliente' => (string) $r['cliente'],
                'veterinario' => (string) $r['veterinario'],
                'totalSugerido' => $totalSugerido,
                'tieneFactura' => $fid > 0,
                'facturaId' => $fid > 0 ? 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT) : null,
            ];
        }

        return $out;
    }

    public static function aceptarPorStaff(PDO $pdo, int $citaId, bool $esAdmin, ?int $vetIdStaff): array
    {
        if ($citaId <= 0) {
            return ['ok' => false, 'error' => 'Falta citaId', 'code' => 400];
        }

        $st = $pdo->prepare(
            'SELECT CITA_ID_PK, ESTADO_ID_FK, VETERINARIO_ID_FK FROM CITA WHERE CITA_ID_PK = ? LIMIT 1'
        );
        $st->execute([$citaId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (! $row) {
            return ['ok' => false, 'error' => 'Cita no encontrada', 'code' => 404];
        }
        if ((int) $row['ESTADO_ID_FK'] !== 3) {
            return ['ok' => false, 'error' => 'Solo se pueden aceptar citas pendientes.', 'code' => 400];
        }
        if (! $esAdmin) {
            if ($vetIdStaff === null || (int) $row['VETERINARIO_ID_FK'] !== $vetIdStaff) {
                return ['ok' => false, 'error' => 'Esta cita no está asignada a tu usuario.', 'code' => 403];
            }
        }

        $confirmada = self::estadoConfirmadaId($pdo);
        $upd = $pdo->prepare('UPDATE CITA SET ESTADO_ID_FK = ? WHERE CITA_ID_PK = ? AND ESTADO_ID_FK = 3');
        $upd->execute([$confirmada, $citaId]);
        if ($upd->rowCount() === 0) {
            return ['ok' => false, 'error' => 'No se pudo actualizar la cita.', 'code' => 409];
        }

        return ['ok' => true];
    }

    private static function estadoConfirmadaId(PDO $pdo): int
    {
        try {
            $st = $pdo->query("SELECT ESTADO_ID_PK FROM ESTADO WHERE DESCRIPCION = 'Confirmada' LIMIT 1");
            $id = $st->fetchColumn();
            if ($id !== false) {
                return (int) $id;
            }
        } catch (Throwable $e) {
        }

        return 6;
    }
}
