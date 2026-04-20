<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'gestion-usuarios'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <h1 class="h3 fw-bold mb-0">Gestión de clientes</h1>
                    <a href="index.php?r=cliente-formulario" class="btn btn-success">+ Nuevo Cliente</a>
                </div>
                <div id="clientes-alert" class="alert d-none" role="alert"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0" id="tabla-clientes">
                                <thead class="table-light">
                                    <tr><th>Cédula</th><th>Nombre</th><th>Teléfono</th><th>Correo</th><th class="text-end">Acciones</th></tr>
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
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/clientes.js"></script>
    <script>document.addEventListener('DOMContentLoaded', () => Clientes.initListado());</script>
</body>
</html>
