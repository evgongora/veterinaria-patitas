<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Servicio - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'servicios-admin'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?r=servicios-admin">Servicios</a></li><li class="breadcrumb-item active" id="breadcrumbAccion">Nuevo Servicio</li></ol></nav>
                <h1 class="h3 fw-bold mb-3" id="tituloFormulario">Nuevo servicio</h1>
                <div id="alertaServicio"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="formServicio">
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label for="nombre" class="form-label">Nombre</label><input type="text" class="form-control" id="nombre" placeholder="Ej Consulta general"></div>
                                <div class="col-12 col-md-6"><label for="estado" class="form-label">Estado</label><select class="form-select" id="estado"><option value="Activo" selected>Activo</option><option value="Inactivo">Inactivo</option></select></div>
                                <div class="col-12"><label for="descripcion" class="form-label">Descripción</label><textarea class="form-control" id="descripcion" rows="3" placeholder="Descripción breve del servicio"></textarea></div>
                                <div class="col-12 col-md-6"><label for="precio" class="form-label">Precio (₡)</label><input type="number" class="form-control" id="precio" min="0" placeholder="0 = Consultar"><div class="form-text">0 para "Consultar"</div></div>
                                <div class="col-12 col-md-6"><label for="duracionMin" class="form-label">Duración (min)</label><input type="number" class="form-control" id="duracionMin" min="1" placeholder="30"></div>
                                <div class="col-12"><label for="icono" class="form-label">Símbolo del servicio</label><select class="form-select" id="icono"><option value="🩺">Consulta general</option><option value="💉">Vacunas</option><option value="🛁">Baño</option><option value="🐾">Corte de uñas</option><option value="💊">Farmacia</option><option value="✂️">Grooming</option></select></div>
                            </div>
                            <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-success">Guardar Servicio</button><a href="index.php?r=servicios-admin" class="btn btn-outline-secondary">Cancelar</a></div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/validaciones.js"></script>
    <script src="public/js/servicios.js?v=3"></script>
</body>
</html>
