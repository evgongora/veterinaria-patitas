<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="app-layout admin-layout">
        <header class="app-header">
            <a href="index.php?r=panel-admin" class="brand"><span class="paw">🐾</span><span>Veterinaria Patitas</span></a>
            <div class="user-area">
                <div class="user-info">
                    <div class="name">Admin Veterinaria</div>
                    <div class="role">Admin</div>
                </div>
                <a href="index.php?r=login" class="btn-logout">→ Cerrar Sesión</a>
            </div>
        </header>
        <div class="app-body">
            <aside class="app-sidebar">
                <a href="index.php?r=panel-admin" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
                <a href="index.php?r=gestion-usuarios" class="nav-item"><span class="icon">👥</span> Gestión de Usuarios</a>
                <a href="index.php?r=citas-dia" class="nav-item"><span class="icon">📅</span> Citas del Día</a>
                <a href="index.php?r=servicios-admin" class="nav-item"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=inventario" class="nav-item"><span class="icon">📦</span> Inventario</a>
                <a href="index.php?r=reportes" class="nav-item"><span class="icon">📊</span> Reportes</a>
            </aside>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-1">Panel de Administración</h1>
                <p class="text-muted mb-4">Resumen general del sistema</p>

                <section class="row g-3 mb-4">
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Usuarios Registrados</p>
                                    <h2 class="h4 fw-bold mb-0">245</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;color:#166534;font-size:1.5rem;">👥</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Animales Registrados</p>
                                    <h2 class="h4 fw-bold mb-0">523</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dbeafe;color:#1976D2;font-size:1.5rem;">🐕</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Citas Hoy</p>
                                    <h2 class="h4 fw-bold mb-0">12</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;color:#166534;font-size:1.5rem;">📅</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Items en Inventario</p>
                                    <h2 class="h4 fw-bold mb-0">89</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dbeafe;color:#1976D2;font-size:1.5rem;">📦</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Ingresos del Mes</p>
                                    <h2 class="h4 fw-bold mb-0">$15,240</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;color:#166534;font-size:1.5rem;">💰</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted small mb-1">Satisfacción</p>
                                    <h2 class="h4 fw-bold mb-0">94%</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dbeafe;color:#1976D2;font-size:1.5rem;">📈</div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row g-4">
                    <section class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <h2 class="h6 fw-bold">Citas de Hoy</h2>
                            </div>
                            <div class="card-body pt-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>09:00 - Max (Labrador)</strong><br><small class="text-muted">Dueño: Juan Pérez · Dra. González</small></div>
                                        <span class="badge badge-pagado rounded-pill">Confirmada</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>10:30 - Luna (Siamés)</strong><br><small class="text-muted">Dueño: María López · Dr. Ramírez</small></div>
                                        <span class="badge badge-pendiente rounded-pill">En espera</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>12:00 - Rocky (Pastor Alemán)</strong><br><small class="text-muted">Dueño: Carlos Ruiz · Dra. González</small></div>
                                        <span class="badge badge-pagado rounded-pill">Confirmada</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>14:00 - Mimi (Persa)</strong><br><small class="text-muted">Dueño: Ana Torres · Dr. Ramírez</small></div>
                                        <span class="badge bg-secondary rounded-pill">Pendiente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <h2 class="h6 fw-bold">Usuarios Recientes</h2>
                            </div>
                            <div class="card-body pt-2">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>Pedro Sánchez</strong><br><small class="text-muted">pedro@email.com · 4 Mar 2026</small></div>
                                        <span class="badge badge-pagado rounded-pill">Cliente</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>Laura Méndez</strong><br><small class="text-muted">laura@email.com · 3 Mar 2026</small></div>
                                        <span class="badge badge-pagado rounded-pill">Cliente</span>
                                    </div>
                                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                        <div><strong>Diego Castro</strong><br><small class="text-muted">diego@email.com · 2 Mar 2026</small></div>
                                        <span class="badge badge-pagado rounded-pill">Cliente</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
</body>
</html>
