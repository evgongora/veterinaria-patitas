<?php
/**
 * GET: catálogo de razas con especie (para formularios)
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

$sql = <<<'SQL'
SELECT r.RAZA_ID_PK AS id, r.NOMBRE AS raza, e.ESPECIE_ID_PK AS especieId, e.ESPECIE AS especie
FROM RAZA r
JOIN ESPECIE e ON e.ESPECIE_ID_PK = r.ESPECIE_ID_FK
ORDER BY e.ESPECIE ASC, r.NOMBRE ASC
SQL;

$razas = $pdo->query($sql)->fetchAll();

api_json(['ok' => true, 'razas' => $razas]);
