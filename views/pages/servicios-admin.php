<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Servicios - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="app-layout admin-layout">
        <header class="app-header">
            <a href="index.php?r=panel-admin" class="brand"><span class="paw">🐾</span><span>Veterinaria Patitas</span></a>
            <div class="user-area">
                <div class="user-info"><div class="name">Admin Veterinaria</div><div class="role">Admin</div></div>
                <a href="index.php?r=login" class="btn-logout">→ Cerrar Sesión</a>
            </div>
        </header>
        <div class="app-body">
            <aside class="app-sidebar">
                <a href="index.php?r=panel-admin" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
                <a href="index.php?r=gestion-usuarios" class="nav-item"><span class="icon">👥</span> Gestión de Usuarios</a>
                <a href="index.php?r=citas-dia" class="nav-item"><span class="icon">📅</span> Citas del Día</a>
                <a href="index.php?r=servicios-admin" class="nav-item active"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=inventario" class="nav-item"><span class="icon">📦</span> Inventario</a>
                <a href="index.php?r=reportes" class="nav-item"><span class="icon">📊</span> Reportes</a>
            </aside>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Gestión de Servicios</h1>
                        <p class="text-muted mb-0">Agrega y administra los servicios de la clínica</p>
                    </div>
                    <a href="index.php?r=servicio-formulario" class="btn btn-success">+ Nuevo Servicio</a>
                </div>

                <section class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Servicios activos</p>
                                    <h2 class="h4 fw-bold text-success mb-0" id="statActivos">0</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#dcfce7;color:#166534;">✓</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="text-muted small mb-1">Servicios inactivos</p>
                                    <h2 class="h4 fw-bold text-secondary mb-0" id="statInactivos">0</h2>
                                </div>
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#e5e7eb;color:#374151;">⏸</div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="d-flex flex-wrap gap-2 mb-3">
                    <input type="text" class="form-control" id="txtBuscarServicio" placeholder="Buscar servicios..." style="max-width:240px">
                    <select class="form-select" id="selEstadoServicio" style="max-width:160px">
                        <option value="Todos" selected>Todos</option>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltros">Limpiar</button>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="d-none d-md-table-cell">Descripción</th>
                                        <th>Precio</th>
                                        <th>Duración</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyServicios"></tbody>
                            </table>
                        </div>
                        <div class="text-muted small p-3" id="txtResumenServicios"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
    <script src="js/servicios.js"></script>
</body>
</html>
