<?php

declare(strict_types=1);

/**
 * Catálogos auxiliares (razas, tipos de cita).
 */
final class Catalogo
{

    public static function razas(PDO $pdo): array
    {
        $sql = <<<'SQL'
SELECT r.RAZA_ID_PK AS id, r.NOMBRE AS raza, e.ESPECIE_ID_PK AS especieId, e.ESPECIE AS especie
FROM RAZA r
JOIN ESPECIE e ON e.ESPECIE_ID_PK = r.ESPECIE_ID_FK
ORDER BY e.ESPECIE ASC, r.NOMBRE ASC
SQL;

        $razas = $pdo->query($sql)->fetchAll();

        return ['ok' => true, 'razas' => $razas];
    }

    public static function tiposCita(PDO $pdo): array
    {
        $rows = $pdo->query('SELECT TIPO_DE_CITA_ID_PK AS id, DESCRIPCION AS nombre FROM TIPO_DE_CITA ORDER BY TIPO_DE_CITA_ID_PK ASC')->fetchAll();

        return ['ok' => true, 'tipos' => $rows];
    }
}
