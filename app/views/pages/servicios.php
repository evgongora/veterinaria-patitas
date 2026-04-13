<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicios - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'servicios'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-1">Nuestros Servicios</h1>
                <p class="text-muted mb-4">Elige un servicio para agendar tu cita</p>
                <div class="row g-4" id="cardsServicios"></div>
                <div id="servicios-empty" class="text-center py-5 text-muted d-none">
                    <p class="mb-0">No hay servicios disponibles en este momento.</p>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/servicios.js?v=3"></script>
</body>
</html>
