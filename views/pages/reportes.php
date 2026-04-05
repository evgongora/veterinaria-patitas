<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Veterinaria Patitas</title>
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
                <a href="index.php?r=servicios-admin" class="nav-item"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=inventario" class="nav-item"><span class="icon">📦</span> Inventario</a>
                <a href="index.php?r=reportes" class="nav-item active"><span class="icon">📊</span> Reportes</a>
            </aside>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-1">Reportes</h1>
                <p class="text-muted mb-4">Informes y estadísticas del sistema</p>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <p class="text-muted mb-0">Módulo de reportes en desarrollo.</p>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
</body>
</html>
