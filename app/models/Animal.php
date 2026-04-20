<?php

declare(strict_types=1);

/**
 * Modelo ANIMAL — mascotas.
 */
final class Animal
{
    public static function listarParaUsuario(PDO $pdo, bool $esCliente, ?int $clienteId): array
    {
        $sql = <<<'SQL'
SELECT
    a.ANIMAL_ID_PK,
    a.NOMBRE,
    a.EDAD,
    a.SEXO,
    a.PESO,
    a.OBSERVACIONES,
    e.ESPECIE AS especie,
    r.NOMBRE AS raza,
    r.RAZA_ID_PK AS razaId,
    CONCAT(c.NOMBRE, ' ', c.APELLIDO_1) AS propietario
FROM ANIMAL a
JOIN RAZA r ON r.RAZA_ID_PK = a.RAZATB_ID_FK
JOIN ESPECIE e ON e.ESPECIE_ID_PK = r.ESPECIE_ID_FK
JOIN CLIENTE c ON c.CLIENTE_ID_PK = a.CLIENTE_ID_FK
SQL;

        if ($esCliente) {
            if ($clienteId === null) {
                return [];
            }
            $sql .= ' WHERE a.CLIENTE_ID_FK = ? ORDER BY a.NOMBRE ASC';
            $st = $pdo->prepare($sql);
            $st->execute([$clienteId]);
        } else {
            $sql .= ' ORDER BY c.NOMBRE ASC, a.NOMBRE ASC';
            $st = $pdo->query($sql);
        }

        $rows = $st->fetchAll();
        $out = [];
        foreach ($rows as $r) {
            $sexo = (string) ($r['SEXO'] ?? '');
            $sexoTxt = $sexo === 'F' ? 'Hembra' : ($sexo === 'M' ? 'Macho' : $sexo);
            $out[] = [
                'id' => (int) $r['ANIMAL_ID_PK'],
                'nombre' => (string) $r['NOMBRE'],
                'especie' => (string) $r['especie'],
                'raza' => (string) $r['raza'],
                'razaId' => (int) $r['razaId'],
                'edad' => $r['EDAD'] !== null ? (int) $r['EDAD'] : null,
                'sexo' => $sexoTxt,
                'sexoCodigo' => $sexo,
                'peso' => $r['PESO'] !== null ? (float) $r['PESO'] : null,
                'observaciones' => (string) ($r['OBSERVACIONES'] ?? ''),
                'propietario' => (string) $r['propietario'],
            ];
        }

        return $out;
    }

    public static function crearOActualizarCliente(PDO $pdo, int $clienteId, array $body): array
    {
        $animalIdEdit = isset($body['animalId']) ? (int) $body['animalId'] : 0;
        $nombre = isset($body['nombre']) ? trim((string) $body['nombre']) : '';
        $razaId = isset($body['razaId']) ? (int) $body['razaId'] : 0;
        $edad = isset($body['edad']) ? (int) $body['edad'] : 0;
        $sexo = isset($body['sexo']) ? strtoupper(trim((string) $body['sexo'])) : '';
        $peso = isset($body['peso']) ? (float) $body['peso'] : 0.0;
        $obs = isset($body['observaciones']) ? trim((string) $body['observaciones']) : '';

        if ($nombre === '' || $razaId <= 0) {
            return ['ok' => false, 'error' => 'Nombre y raza son obligatorios', 'code' => 400];
        }

        if ($sexo !== 'M' && $sexo !== 'F') {
            return ['ok' => false, 'error' => 'Sexo debe ser M o F', 'code' => 400];
        }

        $st = $pdo->prepare('SELECT RAZA_ID_PK FROM RAZA WHERE RAZA_ID_PK = ?');
        $st->execute([$razaId]);
        if (! $st->fetch()) {
            return ['ok' => false, 'error' => 'Raza no válida', 'code' => 400];
        }

        if ($animalIdEdit > 0) {
            $st = $pdo->prepare('SELECT CLIENTE_ID_FK FROM ANIMAL WHERE ANIMAL_ID_PK = ?');
            $st->execute([$animalIdEdit]);
            $own = $st->fetchColumn();
            if ($own === false || (int) $own !== $clienteId) {
                return ['ok' => false, 'error' => 'No autorizado', 'code' => 403];
            }
            $upd = $pdo->prepare(
                'UPDATE ANIMAL SET RAZATB_ID_FK = ?, NOMBRE = ?, PESO = ?, EDAD = ?, SEXO = ?, OBSERVACIONES = ? WHERE ANIMAL_ID_PK = ?'
            );
            $upd->execute([
                $razaId,
                $nombre,
                $peso,
                $edad > 0 ? $edad : null,
                $sexo,
                $obs !== '' ? $obs : null,
                $animalIdEdit,
            ]);

            return ['ok' => true, 'animalId' => $animalIdEdit];
        }

        $st = $pdo->query('SELECT COALESCE(MAX(ANIMAL_ID_PK), 0) + 1 AS n FROM ANIMAL');
        $nextId = (int) $st->fetchColumn();

        $ins = $pdo->prepare(
            'INSERT INTO ANIMAL (ANIMAL_ID_PK, RAZATB_ID_FK, ESTADO_ID_FK, CLIENTE_ID_FK, NOMBRE, PESO, EDAD, SEXO, OBSERVACIONES)
             VALUES (?, ?, 1, ?, ?, ?, ?, ?, ?)'
        );
        $ins->execute([
            $nextId,
            $razaId,
            $clienteId,
            $nombre,
            $peso,
            $edad > 0 ? $edad : null,
            $sexo,
            $obs !== '' ? $obs : null,
        ]);

        return ['ok' => true, 'animalId' => $nextId];
    }
}
