<?php
declare(strict_types=1);
if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . page('login'), true, 302);
    exit;
}
$esStaff = patitas_es_staff();
$layoutClass = $esStaff ? 'app-layout admin-layout' : 'app-layout';
$headerHome = $esStaff ? 'index.php?r=panel-admin' : 'index.php?r=panel';
?>
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
    <div class="<?php echo htmlspecialchars($layoutClass, ENT_QUOTES, 'UTF-8'); ?>">
        <?php $patitasHeaderHome = $headerHome; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'pagos'; ?>
            <?php if ($esStaff) : ?>
                <?php require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <?php else : ?>
                <?php require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <?php endif; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-1" id="tituloPagos">Pagos</h1>
                <?php if ($esStaff) : ?>
                <p class="text-muted small mb-3">Consulta todos los pagos y registra cobros por cita atendida.</p>

                <div class="card border-0 shadow-sm mb-4" id="cardRegistrarPago">
                    <div class="card-body">
                        <h2 class="h6 fw-bold mb-3">Registrar pago por cita</h2>
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-lg-5">
                                <label class="form-label mb-1" for="selCitaCobro">Cita</label>
                                <select class="form-select" id="selCitaCobro">
                                    <option value="">Cargando…</option>
                                </select>
                                <div class="form-text" id="txtDetalleCitaCobro"></div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-2">
                                <label class="form-label mb-1" for="numMontoPago">Monto (₡)</label>
                                <input type="number" class="form-control" id="numMontoPago" min="1" step="1" placeholder="0">
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <label class="form-label mb-1" for="selMetodoPagoRegistro">Método</label>
                                <select class="form-select" id="selMetodoPagoRegistro">
                                    <option value="">—</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-2">
                                <button type="button" class="btn btn-success w-100" id="btnRegistrarPago">Registrar pago</button>
                            </div>
                        </div>
                        <div class="mt-3" id="alertRegistrarPago" role="alert"></div>
                    </div>
                </div>
                <?php else : ?>
                <p class="text-muted small mb-3">Pagos asociados a tus facturas.</p>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label class="form-label mb-1" for="txtBuscarPago">Buscar</label>
                                <input class="form-control" id="txtBuscarPago" placeholder="Pago, factura, método<?php echo $esStaff ? ', cliente' : ''; ?>…">
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
                                    <?php if ($esStaff) : ?>
                                    Listado según base de datos. Los clientes solo ven sus propios pagos en su panel.
                                    <?php else : ?>
                                    Solo se muestran pagos vinculados a tus facturas.
                                    <?php endif; ?>
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
                                        <th class="patitas-col-pago-cliente d-none d-lg-table-cell">Cliente</th>
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
    <script>window.patitasPagosModo = <?php echo $esStaff ? "'staff'" : "'cliente'"; ?>;</script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/facturacion.js?v=4"></script>
</body>
</html>
