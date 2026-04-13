<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'inventario'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <div id="alertaSoloStaff"></div>

                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <h1 class="h3 fw-bold mb-0">Inventario</h1>
                    <a href="index.php?r=inventario-formulario" id="btnAgregarMedicamento" class="btn btn-success">+ Agregar medicamento</a>
                </div>

                <div id="bannerStock" class="mb-3"></div>

                <div id="seccionInventario">
                    <section class="row g-3 mb-4">
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">Total Items</p>
                                    <h2 class="h4 fw-bold mb-0" id="statTotal">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">Stock Normal</p>
                                    <h2 class="h4 fw-bold text-success mb-0" id="statNormal">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">Stock Bajo</p>
                                    <h2 class="h4 fw-bold text-warning mb-0" id="statBajo">—</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <p class="text-muted small mb-1">Stock Crítico</p>
                                    <h2 class="h4 fw-bold text-danger mb-0" id="statCritico">—</h2>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Medicamento</th>
                                            <th>Cantidad</th>
                                            <th>Vencimiento</th>
                                            <th>Tipo / proveedor</th>
                                            <th>Estado</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyInventario"></tbody>
                                </table>
                            </div>
                            <div class="text-muted small p-3" id="txtResumenInventario"></div>
                        </div>
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
