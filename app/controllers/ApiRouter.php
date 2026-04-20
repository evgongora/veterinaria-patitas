<?php

declare(strict_types=1);

/**
 * Front controller de la capa API — despacha ?route= hacia el controlador correspondiente.
 */
final class ApiRouter
{
    private const ROUTES = [
        'auth',
        'registro',
        'logout',
        'dashboard',
        'citas',
        'animales',
        'clientes',
        'inventario',
        'servicios',
        'facturas',
        'pagos',
        'historial',
        'evaluaciones',
        'razas',
        'tipos-cita',
        'veterinarios',
        'ping',
    ];

    public static function dispatch(): void
    {
        $route = isset($_GET['route']) ? trim((string) $_GET['route']) : '';
        if ($route === '' || ! in_array($route, self::ROUTES, true)) {
            api_json(['ok' => false, 'error' => 'Ruta API no encontrada'], 404);
        }

        match ($route) {
            'auth' => AuthController::handle(),
            'registro' => AuthController::registro(),
            'logout' => AuthController::logout(),
            'dashboard' => DashboardController::index(),
            'citas' => CitaController::handle(),
            'animales' => AnimalController::handle(),
            'clientes' => ClinicaController::clientes(),
            'inventario' => ClinicaController::inventario(),
            'servicios' => ClinicaController::servicios(),
            'facturas' => FacturaController::handle(),
            'pagos' => PagoController::handle(),
            'historial' => HistorialController::handle(),
            'evaluaciones' => EvaluacionController::handle(),
            'razas' => ClinicaController::razas(),
            'tipos-cita' => ClinicaController::tiposCita(),
            'veterinarios' => VeterinarioController::dispatch(),
            'ping' => PingController::handle(),
        };
    }
}
