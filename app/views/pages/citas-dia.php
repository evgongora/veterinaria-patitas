<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas del día - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'citas-dia'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Citas del día</h1>
                        <p class="text-muted mb-0" id="subtituloCitasDia">Cargando agenda…</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success rounded-pill px-3 py-2">Hoy <span id="txtFechaHoy">—</span></span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm patitas-stat-pop">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Hora</th>
                                        <th>Mascota</th>
                                        <th>Veterinario</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>ID</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyCitasHoy"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p class="text-muted small mt-3 mb-0">
                    <a href="index.php?r=citas" class="text-success">Ver listado completo de citas</a> (cliente y administración).
                </p>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/citas-dia.js?v=1"></script>
</body>
</html>
