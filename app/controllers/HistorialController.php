<?php

declare(strict_types=1);

/**
 * Historial clínico por animal.
 */
final class HistorialController
{
    public static function handle(): void
    {
        api_require_method('GET');

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        api_require_login();

        $animalId = isset($_GET['animalId']) ? (int) $_GET['animalId'] : 0;
        $cid = api_is_cliente() ? api_cliente_id($pdo) : null;

        $r = Historial::porAnimal($pdo, $animalId, api_is_cliente(), $cid);
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json($r);
    }
}
