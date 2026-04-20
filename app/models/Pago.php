<?php

declare(strict_types=1);

/**
 * Modelo PAGO.
 */
final class Pago
{
    public static function mapEstado(string $desc): string
    {
        $d = mb_strtolower($desc);
        if (str_contains($d, 'pagad')) {
            return 'Exitoso';
        }
        if (str_contains($d, 'no pag')) {
            return 'Pendiente';
        }

        return $desc;
    }

    public static function listar(PDO $pdo, bool $esCliente, string $sessionEmail): array
    {
        $sql = <<<'SQL'
SELECT
    p.PAGO_ID_PK,
    p.MONTO_TOTAL,
    p.FECHA_DE_PAGO,
    f.FACTURA_ID_PK,
    mp.METODO,
    ep.DESCRIPCION AS estado_raw,
    uc.EMAIL AS cliente_email
FROM PAGO p
JOIN FACTURA f ON f.FACTURA_ID_PK = p.FACTURA_ID_FK
JOIN CITA c ON c.CITA_ID_PK = f.CITA_ID_FK
JOIN ANIMAL a ON a.ANIMAL_ID_PK = c.ANIMAL_ID_FK
JOIN CLIENTE cl ON cl.CLIENTE_ID_PK = a.CLIENTE_ID_FK
LEFT JOIN USUARIO uc ON uc.USUARIO_ID_PK = cl.USUARIO_ID_FK
JOIN METODO_PAGO mp ON mp.METODOPAGO_ID_PK = p.METODOPAGO_ID_FK
JOIN ESTADO ep ON ep.ESTADO_ID_PK = p.ESTADO_ID_FK
ORDER BY p.FECHA_DE_PAGO DESC
SQL;

        $rows = $pdo->query($sql)->fetchAll();
        $sessionEmail = strtolower($sessionEmail);

        $out = [];
        foreach ($rows as $r) {
            $email = strtolower((string) ($r['cliente_email'] ?? ''));
            if ($esCliente && $email !== $sessionEmail) {
                continue;
            }
            $pid = (int) $r['PAGO_ID_PK'];
            $fid = (int) $r['FACTURA_ID_PK'];
            $fecha = (string) $r['FECHA_DE_PAGO'];
            if (strlen($fecha) > 10) {
                $fecha = substr($fecha, 0, 10);
            }
            $out[] = [
                'id' => 'PAG-' . str_pad((string) $pid, 3, '0', STR_PAD_LEFT),
                'facturaId' => 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT),
                'clienteEmail' => (string) ($r['cliente_email'] ?? ''),
                'monto' => (float) $r['MONTO_TOTAL'],
                'fecha' => $fecha,
                'metodo' => (string) $r['METODO'],
                'estado' => self::mapEstado((string) $r['estado_raw']),
            ];
        }

        return ['ok' => true, 'pagos' => $out];
    }

    /** @return list<array{id: int, nombre: string}> */
    public static function listarMetodosPago(PDO $pdo): array
    {
        $sql = 'SELECT METODOPAGO_ID_PK, METODO FROM METODO_PAGO ORDER BY METODOPAGO_ID_PK ASC';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id' => (int) $r['METODOPAGO_ID_PK'],
                'nombre' => (string) $r['METODO'],
            ];
        }

        return $out;
    }

    /**
     * Registra un pago exitoso vinculado a una cita (personal). Crea factura si no existe.
     *
     * @return array{ok: true, pagoId: string, facturaId: string}|array{ok: false, error: string, code: int}
     */
    public static function registrarDesdeCita(PDO $pdo, array $body, bool $esAdmin, ?int $vetIdStaff): array
    {
        $citaId = isset($body['citaId']) ? (int) $body['citaId'] : 0;
        $metodoPagoId = isset($body['metodoPagoId']) ? (int) $body['metodoPagoId'] : 0;
        $monto = isset($body['monto']) ? (float) $body['monto'] : 0.0;

        if ($citaId <= 0 || $metodoPagoId <= 0) {
            return ['ok' => false, 'error' => 'Indica la cita y el método de pago', 'code' => 400];
        }
        if ($monto <= 0) {
            return ['ok' => false, 'error' => 'El monto debe ser mayor a cero', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT 1 FROM METODO_PAGO WHERE METODOPAGO_ID_PK = ? LIMIT 1');
        $st->execute([$metodoPagoId]);
        if (! $st->fetchColumn()) {
            return ['ok' => false, 'error' => 'Método de pago no válido', 'code' => 400];
        }

        $st = $pdo->prepare(
            'SELECT CITA_ID_PK, VETERINARIO_ID_FK, ESTADO_ID_FK FROM CITA WHERE CITA_ID_PK = ? LIMIT 1'
        );
        $st->execute([$citaId]);
        $cita = $st->fetch(PDO::FETCH_ASSOC);
        if (! $cita) {
            return ['ok' => false, 'error' => 'Cita no encontrada', 'code' => 404];
        }
        $estadoCita = (int) $cita['ESTADO_ID_FK'];
        if (in_array($estadoCita, [3, 5], true)) {
            return [
                'ok' => false,
                'error' => 'No se puede registrar pago en citas pendientes de aprobación o canceladas',
                'code' => 400,
            ];
        }
        if (! $esAdmin) {
            if ($vetIdStaff === null || (int) $cita['VETERINARIO_ID_FK'] !== $vetIdStaff) {
                return ['ok' => false, 'error' => 'Solo puedes registrar pagos de citas asignadas a ti', 'code' => 403];
            }
        }

        $st = $pdo->prepare(
            'SELECT FACTURA_ID_PK, TOTAL_A_COBRAR, ESTADO_ID_FK FROM FACTURA WHERE CITA_ID_FK = ? LIMIT 1'
        );
        $st->execute([$citaId]);
        $facturaRow = $st->fetch(PDO::FETCH_ASSOC);

        $st = $pdo->prepare(
            'SELECT COALESCE(SUM(s.PRECIO), 0) FROM SERVICIOS_POR_CITA sp
             INNER JOIN SERVICIO s ON s.SERVICIO_ID_PK = sp.SERVICIO_ID_FK WHERE sp.CITA_ID_FK = ?'
        );
        $st->execute([$citaId]);
        $sumServicios = (float) $st->fetchColumn();

        $fid = 0;
        if ($facturaRow) {
            $fid = (int) $facturaRow['FACTURA_ID_PK'];
            if ((int) $facturaRow['ESTADO_ID_FK'] === 7) {
                return ['ok' => false, 'error' => 'Esta factura ya está pagada', 'code' => 400];
            }
            $st = $pdo->prepare(
                'SELECT 1 FROM PAGO WHERE FACTURA_ID_FK = ? AND ESTADO_ID_FK = 7 LIMIT 1'
            );
            $st->execute([$fid]);
            if ($st->fetchColumn()) {
                return ['ok' => false, 'error' => 'Ya existe un pago registrado para esta factura', 'code' => 400];
            }
            $totalEsperado = (float) $facturaRow['TOTAL_A_COBRAR'];
            if (abs($monto - $totalEsperado) > 0.009) {
                return [
                    'ok' => false,
                    'error' => 'El monto debe coincidir con el total de la factura (₡'
                        . number_format($totalEsperado, 0, ',', ' ') . ')',
                    'code' => 400,
                ];
            }
        } else {
            if ($sumServicios > 0) {
                if (abs($monto - $sumServicios) > 0.009) {
                    return [
                        'ok' => false,
                        'error' => 'El monto debe coincidir con la suma de servicios (₡'
                            . number_format($sumServicios, 0, ',', ' ') . ')',
                        'code' => 400,
                    ];
                }
            }
        }

        $pdo->beginTransaction();
        try {
            if (! $facturaRow) {
                $totalFacturaNueva = $sumServicios > 0 ? $sumServicios : $monto;
                if (abs($monto - $totalFacturaNueva) > 0.009) {
                    $pdo->rollBack();

                    return ['ok' => false, 'error' => 'El monto no coincide con el total a cobrar', 'code' => 400];
                }
                $st = $pdo->query('SELECT COALESCE(MAX(FACTURA_ID_PK), 0) + 1 FROM FACTURA');
                $nextFid = (int) $st->fetchColumn();
                $insF = $pdo->prepare(
                    'INSERT INTO FACTURA (FACTURA_ID_PK, CITA_ID_FK, ESTADO_ID_FK, TOTAL_A_COBRAR, FECHA_EMISION)
                     VALUES (?, ?, 8, ?, ?)'
                );
                $insF->execute([$nextFid, $citaId, $totalFacturaNueva, date('Y-m-d H:i:s')]);
                $fid = $nextFid;
            }

            $st = $pdo->query('SELECT COALESCE(MAX(PAGO_ID_PK), 0) + 1 FROM PAGO');
            $nextPid = (int) $st->fetchColumn();
            $insP = $pdo->prepare(
                'INSERT INTO PAGO (PAGO_ID_PK, ESTADO_ID_FK, FACTURA_ID_FK, METODOPAGO_ID_FK, CITA_ID_FK, MONTO_TOTAL, FECHA_DE_PAGO)
                 VALUES (?, 7, ?, ?, ?, ?, ?)'
            );
            $insP->execute([$nextPid, $fid, $metodoPagoId, $citaId, $monto, date('Y-m-d H:i:s')]);

            $upd = $pdo->prepare('UPDATE FACTURA SET ESTADO_ID_FK = 7 WHERE FACTURA_ID_PK = ?');
            $upd->execute([$fid]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();

            return ['ok' => false, 'error' => 'No se pudo registrar el pago', 'code' => 500];
        }

        return [
            'ok' => true,
            'pagoId' => 'PAG-' . str_pad((string) $nextPid, 3, '0', STR_PAD_LEFT),
            'facturaId' => 'FAC-' . str_pad((string) $fid, 3, '0', STR_PAD_LEFT),
        ];
    }
}
