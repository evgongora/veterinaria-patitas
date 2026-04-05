<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Medicamento - Veterinaria Patitas</title>
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
                <a href="index.php?r=inventario" class="nav-item active"><span class="icon">📦</span> Inventario</a>
                <a href="index.php?r=reportes" class="nav-item"><span class="icon">📊</span> Reportes</a>
            </aside>
            <main class="app-content">
                <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?r=inventario">Inventario</a></li><li class="breadcrumb-item active">Agregar Medicamento</li></ol></nav>
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:56px;height:56px;background:#2E7D32;color:white;font-size:1.5rem;">💊</div>
                    <div>
                        <h1 class="h3 fw-bold mb-1">Agregar Medicamento</h1>
                        <p class="text-muted mb-0">Completa la información del medicamento</p>
                    </div>
                </div>
                <div id="alertaInventarioForm"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="formInventario">
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label for="nombre" class="form-label">Medicamento</label><input type="text" class="form-control" id="nombre" placeholder="Ej Amoxicilina 500mg"></div>
                                <div class="col-12 col-md-3"><label for="cantidad" class="form-label">Cantidad</label><input type="number" class="form-control" id="cantidad" min="0" placeholder="Ej 12"></div>
                                <div class="col-12 col-md-3"><label for="estado" class="form-label">Estado</label><select class="form-select" id="estado"><option value="Normal" selected>Stock Normal</option><option value="Bajo">Stock Bajo</option><option value="Critico">Stock Crítico</option></select></div>
                                <div class="col-12 col-md-6"><label for="vencimiento" class="form-label">Vencimiento</label><input type="date" class="form-control" id="vencimiento"></div>
                                <div class="col-12 col-md-6"><label for="proveedor" class="form-label">Proveedor</label><input type="text" class="form-control" id="proveedor" placeholder="Ej FarmaVet S.A."></div>
                            </div>
                            <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-success">Guardar Item</button><a href="index.php?r=inventario" class="btn btn-outline-secondary">Cancelar</a></div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
    <script src="js/inventario.js"></script>
</body>
</html>
