<?php
/**
 * GET: tipos de cita
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

$rows = $pdo->query('SELECT TIPO_DE_CITA_ID_PK AS id, DESCRIPCION AS nombre FROM TIPO_DE_CITA ORDER BY TIPO_DE_CITA_ID_PK ASC')->fetchAll();

api_json(['ok' => true, 'tipos' => $rows]);
