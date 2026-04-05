<?php
/**
 * Front controller — única entrada HTTP para las vistas (patrón MVC front).
 */

declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/PageController.php';

$controller = new PageController();
$controller->dispatch();
