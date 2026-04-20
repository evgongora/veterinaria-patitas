<?php
/**
 * Entrada única HTTP JSON — enruta a controladores MVC (?route=nombre).
 */

declare(strict_types=1);

require_once __DIR__ . '/config/api_bootstrap.php';

ApiRouter::dispatch();
