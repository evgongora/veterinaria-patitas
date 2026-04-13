<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'panel'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <section class="patitas-welcome-hero mb-4" aria-labelledby="txtBienvenida">
                    <p class="patitas-welcome-kicker mb-1" id="patitasFechaHoy"></p>
                    <h1 class="patitas-welcome-title mb-2" id="txtBienvenida">¡Hola!</h1>
                    <p class="patitas-welcome-lead text-muted mb-0">Gestiona el cuidado de tus mascotas desde aquí</p>
                </section>

                <section class="mb-5">
                    <h2 class="h5 fw-bold mb-3">Acciones rápidas</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=mascota-formulario" class="card text-decoration-none text-dark border-0 shadow-sm h-100 patitas-quick-card">
                                <div class="card-body text-center py-4">
                                    <div class="patitas-icon-circle" aria-hidden="true"><i class="bi bi-plus-lg"></i></div>
                                    <h3 class="h6 fw-bold mb-1">Registrar animal</h3>
                                    <p class="small text-muted mb-0">Agrega una nueva mascota</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=cita-formulario" class="card text-decoration-none text-dark border-0 shadow-sm h-100 patitas-quick-card">
                                <div class="card-body text-center py-4">
                                    <div class="patitas-icon-circle" aria-hidden="true"><i class="bi bi-calendar-plus"></i></div>
                                    <h3 class="h6 fw-bold mb-1">Agendar cita</h3>
                                    <p class="small text-muted mb-0">Programa una consulta</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=servicios" class="card text-decoration-none text-dark border-0 shadow-sm h-100 patitas-quick-card">
                                <div class="card-body text-center py-4">
                                    <div class="patitas-icon-circle" aria-hidden="true"><i class="bi bi-heart-pulse"></i></div>
                                    <h3 class="h6 fw-bold mb-1">Ver servicios</h3>
                                    <p class="small text-muted mb-0">Consulta el catálogo</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=historial-clinico" class="card text-decoration-none text-dark border-0 shadow-sm h-100 patitas-quick-card">
                                <div class="card-body text-center py-4">
                                    <div class="patitas-icon-circle" aria-hidden="true"><i class="bi bi-journal-medical"></i></div>
                                    <h3 class="h6 fw-bold mb-1">Historial clínico</h3>
                                    <p class="small text-muted mb-0">Ver registros médicos</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=facturas" class="card text-decoration-none text-dark border-0 shadow-sm h-100 patitas-quick-card">
                                <div class="card-body text-center py-4">
                                    <div class="patitas-icon-circle" aria-hidden="true"><i class="bi bi-receipt-cutoff"></i></div>
                                    <h3 class="h6 fw-bold mb-1">Mis facturas</h3>
                                    <p class="small text-muted mb-0">Revisar pagos</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Mis animales</h2>
                        <a href="index.php?r=mascotas" class="text-success text-decoration-none fw-semibold">Ver todos</a>
                    </div>
                    <div class="row g-3" id="contenedor-mascotas-resumen"></div>
                </section>

                <section>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Próximas citas</h2>
                        <a href="index.php?r=cita-formulario" class="text-success text-decoration-none fw-semibold">Agendar nueva</a>
                    </div>
                    <div class="row g-3" id="contenedor-citas-proximas"></div>
                </section>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/panel.js?v=2"></script>
</body>
</html>
