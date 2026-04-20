<?php

declare(strict_types=1);

/**
 * Historial clínico por animal.
 */
final class Historial
{
    public static function porAnimal(PDO $pdo, int $animalId, bool $esCliente, ?int $clienteId): array
    {
        if ($animalId <= 0) {
            return ['ok' => false, 'error' => 'animalId requerido', 'code' => 400];
        }

        $st = $pdo->prepare(
            'SELECT a.CLIENTE_ID_FK FROM ANIMAL a WHERE a.ANIMAL_ID_PK = ?'
        );
        $st->execute([$animalId]);
        $owner = $st->fetchColumn();
        if ($owner === false) {
            return ['ok' => false, 'error' => 'Animal no encontrado', 'code' => 404];
        }

        if ($esCliente) {
            if ($clienteId === null || (int) $owner !== $clienteId) {
                return ['ok' => false, 'error' => 'No autorizado', 'code' => 403];
            }
        }

        $sql = <<<'SQL'
SELECT
    c.FECHA,
    c.NOTAS_VETERINARIO,
    t.DESCRIPCION AS tipo,
    CONCAT(v.NOMBRE, ' ', v.PRIMER_APELLIDO) AS veterinario
FROM CITA c
JOIN VETERINARIO v ON v.VETERINARIO_ID_PK = c.VETERINARIO_ID_FK
JOIN TIPO_DE_CITA t ON t.TIPO_DE_CITA_ID_PK = c.TIPO_DE_CITA_ID_FK
WHERE c.ANIMAL_ID_FK = ?
ORDER BY c.FECHA DESC
SQL;
        $st = $pdo->prepare($sql);
        $st->execute([$animalId]);
        $rows = $st->fetchAll();

        $out = [];
        foreach ($rows as $r) {
            $fecha = (string) $r['FECHA'];
            $notas = (string) ($r['NOTAS_VETERINARIO'] ?? '');
            $out[] = [
                'fecha' => $fecha,
                'diagnostico' => (string) $r['tipo'],
                'tratamiento' => $notas !== '' ? $notas : '—',
                'veterinario' => (string) $r['veterinario'],
            ];
        }

        return ['ok' => true, 'registros' => $out];
    }
}
