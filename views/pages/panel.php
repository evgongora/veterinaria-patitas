<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="app-layout">
        <header class="app-header">
            <a href="index.php?r=panel" class="brand">
                <span class="paw">🐾</span>
                <span>Veterinaria Patitas</span>
            </a>
            <div class="user-area">
                <div class="user-info">
                    <div class="name" id="userName">Juan Pérez</div>
                    <div class="role">Cliente</div>
                </div>
                <a href="index.php?r=login" class="btn-logout">→ Cerrar Sesión</a>
            </div>
        </header>
        <div class="app-body">
            <aside class="app-sidebar">
                <a href="index.php?r=panel" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
                <a href="index.php?r=mascotas" class="nav-item"><span class="icon">🐾</span> Mis Animales</a>
                <a href="index.php?r=cita-formulario" class="nav-item"><span class="icon">📅</span> Agendar Cita</a>
                <a href="index.php?r=servicios" class="nav-item"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=historial-clinico" class="nav-item"><span class="icon">📋</span> Historial Clínico</a>
                <a href="index.php?r=facturas" class="nav-item"><span class="icon">🧾</span> Facturas</a>
            </aside>
            <main class="app-content">
                <h1 class="fw-bold mb-1" style="font-size:1.75rem;">BIENVENIDO, Juan Pérez!</h1>
                <p class="text-muted mb-4">Gestiona el cuidado de tus mascotas desde aquí</p>

                <section class="mb-5">
                    <h2 class="h5 fw-bold mb-3">Acciones Rápidas</h2>
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=mascota-formulario" class="card text-decoration-none text-dark border-0 shadow-sm h-100">
                                <div class="card-body text-center py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;background:#2E7D32;color:white;font-size:1.5rem;">+</div>
                                    <h3 class="h6 fw-bold mb-1">Registrar Animal</h3>
                                    <p class="small text-muted mb-0">Agrega una nueva mascota</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=cita-formulario" class="card text-decoration-none text-dark border-0 shadow-sm h-100">
                                <div class="card-body text-center py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;background:#1976D2;color:white;font-size:1.5rem;">📅</div>
                                    <h3 class="h6 fw-bold mb-1">Agendar Cita</h3>
                                    <p class="small text-muted mb-0">Programa una consulta</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=servicios" class="card text-decoration-none text-dark border-0 shadow-sm h-100">
                                <div class="card-body text-center py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;background:#2E7D32;color:white;font-size:1.5rem;">🩺</div>
                                    <h3 class="h6 fw-bold mb-1">Ver Servicios</h3>
                                    <p class="small text-muted mb-0">Consulta el catálogo</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=historial-clinico" class="card text-decoration-none text-dark border-0 shadow-sm h-100">
                                <div class="card-body text-center py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;background:#2E7D32;color:white;font-size:1.5rem;">📋</div>
                                    <h3 class="h6 fw-bold mb-1">Historial Clínico</h3>
                                    <p class="small text-muted mb-0">Ver registros médicos</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="index.php?r=facturas" class="card text-decoration-none text-dark border-0 shadow-sm h-100">
                                <div class="card-body text-center py-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:56px;height:56px;background:#1976D2;color:white;font-size:1.5rem;">🧾</div>
                                    <h3 class="h6 fw-bold mb-1">Mis Facturas</h3>
                                    <p class="small text-muted mb-0">Revisar pagos</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <section class="mb-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Mis Animales</h2>
                        <a href="index.php?r=mascotas" class="text-success text-decoration-none fw-semibold">Ver todos</a>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="pet-avatar">🐕</div>
                                    <div class="flex-grow-1">
                                        <strong>Max</strong>
                                        <span class="text-muted">Perro - Labrador</span>
                                    </div>
                                    <span class="text-muted small">3 años</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="pet-avatar">🐱</div>
                                    <div class="flex-grow-1">
                                        <strong>Luna</strong>
                                        <span class="text-muted">Gato - Siamés</span>
                                    </div>
                                    <span class="text-muted small">2 años</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h5 fw-bold mb-0">Próximas Citas</h2>
                        <a href="index.php?r=cita-formulario" class="text-primary text-decoration-none fw-semibold">Agendar nueva</a>
                    </div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="pet-avatar">🐕</div>
                                    <div class="flex-grow-1">
                                        <strong>Max</strong>
                                        <p class="mb-0 small text-muted">10:00 AM · Dra. María González</p>
                                    </div>
                                    <span class="text-muted small">8 Mar 2026</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body d-flex align-items-center gap-3">
                                    <div class="pet-avatar">🐱</div>
                                    <div class="flex-grow-1">
                                        <strong>Luna</strong>
                                        <p class="mb-0 small text-muted">3:00 PM · Dr. Carlos Ramírez</p>
                                    </div>
                                    <span class="text-muted small">15 Mar 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
</body>
</html>
