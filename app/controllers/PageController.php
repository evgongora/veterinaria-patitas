<?php
/**
 * Controlador de páginas — enruta la petición GET ?r= a la vista correspondiente.
 */

declare(strict_types=1);

class PageController
{
    /** @var list<string> */
    private array $routes = [
        'home',
        'login',
        'registro',
        'panel',
        'panel-admin',
        'gestion-usuarios',
        'clientes',
        'cliente-formulario',
        'veterinarios',
        'veterinario-formulario',
        'citas-dia',
        'citas-pendientes',
        'inventario',
        'inventario-formulario',
        'reportes',
        'servicios-admin',
        'servicio-formulario',
        'mascotas',
        'mascota-formulario',
        'cita-formulario',
        'cita-confirmacion',
        'citas',
        'servicios',
        'historial-clinico',
        'facturas',
        'factura-detalle',
        'pagos',
        'evaluacion',
    ];

    public function dispatch(): void
    {
        $route = isset($_GET['r']) ? (string) $_GET['r'] : 'home';
        $route = trim($route);

        if (! in_array($route, $this->routes, true)) {
            $route = '404';
        }

        $viewFile = ROOT_PATH . '/views/pages/' . $route . '.php';

        if (! is_file($viewFile)) {
            $route = '404';
            $viewFile = ROOT_PATH . '/views/pages/404.php';
        }

        require $viewFile;
    }
}
