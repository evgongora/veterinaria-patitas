<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinarios - Veterinaria Patitas</title>
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
                <a href="index.php?r=gestion-usuarios" class="nav-item active"><span class="icon">👥</span> Gestión de Usuarios</a>
                <a href="index.php?r=citas-dia" class="nav-item"><span class="icon">📅</span> Citas del Día</a>
                <a href="index.php?r=servicios-admin" class="nav-item"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=inventario" class="nav-item"><span class="icon">📦</span> Inventario</a>
                <a href="index.php?r=reportes" class="nav-item"><span class="icon">📊</span> Reportes</a>
            </aside>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Gestión de Veterinarios</h1>
                        <p class="text-muted mb-0">Listado de veterinarios activos</p>
                    </div>
                    <a href="index.php?r=veterinario-formulario" class="btn btn-success">+ Nuevo Veterinario</a>
                </div>
                <div id="veterinarios-alert" class="alert d-none" role="alert"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tabla-veterinarios">
                                <thead class="table-light">
                                    <tr><th>Cédula</th><th>Nombre</th><th>Especialidad</th><th>Teléfono</th><th class="text-end">Acciones</th></tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
    <script src="js/veterinarios.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => Veterinarios.initListado());</script>
</body>
</html>
