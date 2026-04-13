<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Animales - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'mascotas'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <h1 class="h3 fw-bold mb-0">Mis animales</h1>
                    <a href="index.php?r=mascota-formulario" class="btn btn-success">+ Registrar animal</a>
                </div>

                <div class="row g-4" id="contenedor-mascotas"></div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/mascotas.js?v=4"></script>
</body>
</html>
