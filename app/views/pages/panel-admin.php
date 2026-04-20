<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'panel-admin'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <section class="patitas-admin-hero mb-4" aria-labelledby="patitasAdminTitulo">
                    <p class="patitas-welcome-kicker mb-1" id="patitasFechaHoyAdmin"></p>
                    <h1 class="patitas-welcome-title mb-2" id="patitasAdminTitulo">Panel de administración</h1>
                    <p class="patitas-welcome-lead text-muted mb-0">Resumen general del sistema · equipo clínico</p>
                </section>

                <section class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Clientes registrados</p>
                                    <h2 class="h4 fw-bold mb-0" id="statUsuarios">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-people-fill"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Animales registrados</p>
                                    <h2 class="h4 fw-bold mb-0" id="statAnimales">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-heart-fill"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Citas hoy</p>
                                    <h2 class="h4 fw-bold mb-0" id="statCitasHoy">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-calendar-day"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Ítems en inventario</p>
                                    <h2 class="h4 fw-bold mb-0" id="statInventario">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-box-seam"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Ingresos del mes (pagos)</p>
                                    <h2 class="h4 fw-bold mb-0" id="statIngresos">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-currency-dollar"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4 patitas-stat-pop">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Veterinarios</p>
                                    <h2 class="h4 fw-bold mb-0" id="statVets">—</h2>
                                </div>
                                <div class="patitas-stat-icon" aria-hidden="true"><i class="bi bi-heart-pulse"></i></div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row g-4">
                    <section class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <h2 class="h6 fw-bold">Citas de hoy</h2>
                            </div>
                            <div class="card-body pt-2">
                                <div class="list-group list-group-flush" id="listaCitasHoy"></div>
                            </div>
                        </div>
                    </section>
                    <section class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <h2 class="h6 fw-bold">Usuarios recientes</h2>
                            </div>
                            <div class="card-body pt-2">
                                <div class="list-group list-group-flush" id="listaUsuariosRecientes"></div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/panel-admin.js?v=2"></script>
</body>
</html>
