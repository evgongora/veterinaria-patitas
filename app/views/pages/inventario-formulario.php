<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Medicamento - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'inventario'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?r=inventario">Inventario</a></li><li class="breadcrumb-item active" id="breadcrumbInvAccion">Nuevo medicamento</li></ol></nav>
                <h1 class="h3 fw-bold mb-3" id="tituloInventarioForm">Nuevo medicamento</h1>
                <div id="alertaInventarioForm"></div>
                <div class="card border-0 shadow-sm" id="seccionFormularioInventario">
                    <div class="card-body p-4">
                        <form id="formInventario">
                            <input type="hidden" id="idNum" value="">
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" id="nombre" required placeholder="Ej. Amoxicilina 500 mg"></div>
                                <div class="col-12 col-md-3"><label for="cantidad" class="form-label">Cantidad en stock</label><input type="number" class="form-control" id="cantidad" min="0" value="0" required></div>
                                <div class="col-12 col-md-3"><label for="tipoId" class="form-label">Tipo</label><select class="form-select" id="tipoId" required><option value="">Cargando…</option></select></div>
                            </div>
                            <p class="text-muted small mt-2 mb-0">El estado (normal / bajo / crítico) se calcula automáticamente según la cantidad.</p>
                            <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-success">Guardar</button><a href="index.php?r=inventario" class="btn btn-outline-secondary">Cancelar</a></div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/inventario.js?v=4"></script>
</body>
</html>
