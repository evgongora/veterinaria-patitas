<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'facturas'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-3" id="tituloFacturas">Facturas</h1>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label small" for="txtBuscarFactura">Buscar</label>
                                <input type="search" class="form-control" id="txtBuscarFactura" placeholder="Número, cliente, mascota…">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label small" for="selEstadoFactura">Estado</label>
                                <select class="form-select" id="selEstadoFactura">
                                    <option value="Todos" selected>Todos</option>
                                    <option value="Pagada">Pagada</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="button" class="btn btn-outline-secondary" id="btnLimpiarFiltrosFactura">Limpiar filtros</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th class="d-none d-md-table-cell">Cliente</th>
                                        <th class="d-none d-md-table-cell">Mascota</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyFacturas"></tbody>
                            </table>
                        </div>
                        <div class="text-muted small p-3" id="txtResumenFacturas"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/facturacion.js?v=2"></script>
</body>
</html>
