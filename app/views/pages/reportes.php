<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'reportes'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-1">Reportes operativos</h1>
                <p class="text-muted mb-4">Indicadores en tiempo real desde la base de datos (mismo origen que el panel de administración).</p>

                <div id="reportes-alerta"></div>

                <div class="row g-3 mb-4" id="reportes-grid">
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Clientes</p>
                                <p class="h3 fw-bold mb-0 text-success" id="repClientes">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Animales registrados</p>
                                <p class="h3 fw-bold mb-0" id="repAnimales">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Citas hoy</p>
                                <p class="h3 fw-bold mb-0" id="repCitasHoy">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Citas pendientes (estado)</p>
                                <p class="h3 fw-bold mb-0 text-warning" id="repCitasPend">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Ítems inventario</p>
                                <p class="h3 fw-bold mb-0" id="repInventario">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Veterinarios</p>
                                <p class="h3 fw-bold mb-0" id="repVets">—</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 patitas-stat-pop">
                        <div class="card border-0 shadow-sm border-start border-success border-4">
                            <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <p class="text-muted small mb-1 mb-lg-0">Ingresos del mes (suma de pagos registrados)</p>
                                    <p class="h4 fw-bold text-success mb-0" id="repIngresos">—</p>
                                </div>
                                <span class="badge bg-light text-dark rounded-pill">Mes actual</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/reportes.js?v=1"></script>
</body>
</html>
