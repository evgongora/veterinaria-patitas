<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagos - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'pagos'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-3" id="tituloPagos">Pagos</h1>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label mb-1" for="txtBuscarPago">Buscar</label>
                                <input class="form-control" id="txtBuscarPago" placeholder="Ej. PAG-001 FAC-001 tarjeta">
                            </div>
                            <div class="col-12 col-md-3">
                                <label class="form-label mb-1" for="selEstadoPago">Estado</label>
                                <select class="form-select" id="selEstadoPago">
                                    <option value="Todos" selected>Todos</option>
                                    <option value="Exitoso">Exitoso</option>
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Fallido">Fallido</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="button" class="btn btn-outline-secondary w-100" id="btnLimpiarFiltrosPago">Limpiar filtros</button>
                            </div>
                            <div class="col-12">
                                <div class="small text-muted" id="txtAyudaPago">
                                    Los pagos mostrados provienen de registros en base de datos.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="alertaPagos"></div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pago</th>
                                        <th>Factura</th>
                                        <th>Fecha</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyPagos"></tbody>
                            </table>
                        </div>
                        <div class="text-muted small mt-3" id="txtResumenPagos"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/facturacion.js?v=2"></script>
</body>
</html>
