<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis citas | Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'citas'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Mis citas</h1>
                        <p class="text-muted small mb-0">Consulta el estado de tus citas programadas.</p>
                    </div>
                    <a href="index.php?r=cita-formulario" class="btn btn-success">Agendar cita</a>
                </div>

                <div id="alertCitasLista" class="alert d-none mb-3" role="alert"></div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Veterinario</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCitas">
                                    <tr><td colspan="7" class="text-muted py-4 text-center">Cargando…</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/citas.js?v=6"></script>
</body>
</html>
