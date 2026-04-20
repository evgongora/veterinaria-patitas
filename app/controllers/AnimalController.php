<?php

declare(strict_types=1);

/**
 * Mascotas (ANIMAL) — listado y registro/edición cliente.
 */
final class AnimalController
{
    public static function handle(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        try {
            $pdo = Database::getConnection();
        } catch (Throwable $e) {
            api_json(['ok' => false, 'error' => 'No se pudo conectar a la base de datos'], 503);
        }

        if ($method === 'GET') {
            api_require_login();
            $cid = api_is_cliente() ? api_cliente_id($pdo) : null;
            $animales = Animal::listarParaUsuario($pdo, api_is_cliente(), $cid);
            api_json(['ok' => true, 'animales' => $animales]);
        }

        api_require_method('POST');
        api_require_login();
        api_require_cliente();

        $cid = api_cliente_id($pdo);
        if ($cid === null) {
            api_json(['ok' => false, 'error' => 'Perfil de cliente no encontrado'], 400);
        }

        $r = Animal::crearOActualizarCliente($pdo, $cid, api_json_body());
        if (! $r['ok']) {
            api_json(['ok' => false, 'error' => $r['error']], $r['code']);
        }

        api_json(['ok' => true, 'animalId' => $r['animalId']]);
    }
}
