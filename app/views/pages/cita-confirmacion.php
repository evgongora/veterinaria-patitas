<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita confirmada - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'cita-formulario'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-2">Cita confirmada</h1>
                <p class="text-muted small mb-4">Tu cita quedó registrada. Aquí tienes el resumen.</p>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div id="resumenCita"></div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?r=panel" class="btn btn-primary">Ir al panel</a>
                    <a href="index.php?r=cita-formulario" class="btn btn-outline-secondary">Agendar otra cita</a>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/citas.js?v=6"></script>
</body>
</html>