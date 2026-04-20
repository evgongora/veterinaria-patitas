<?php
declare(strict_types=1);
/* Vista factura-detalle — MVC */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de factura - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'facturas'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h1 class="h3 fw-bold mb-0">Detalle de factura</h1>
                    <div class="d-flex gap-2">
                        <a class="btn btn-outline-secondary" href="index.php?r=facturas">Volver</a>
                        <a class="btn btn-primary" id="btnIrPagos" href="index.php?r=pagos"><?php echo patitas_es_staff() ? 'Ver pagos (clínica)' : 'Ver mis pagos'; ?></a>
                    </div>
                </div>

                <div id="alertaDetalleFactura"></div>

                <div class="row g-3">
                    <div class="col-12 col-lg-7">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-muted small">Factura</div>
                                        <div class="h4 mb-0" id="txtFacturaId">—</div>
                                    </div>
                                    <div id="badgeEstadoFactura"></div>
                                </div>
                                <hr>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-muted small">Fecha</div>
                                        <div class="fw-semibold" id="txtFacturaFecha">-</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Mascota</div>
                                        <div class="fw-semibold" id="txtFacturaMascota">-</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="text-muted small">Cliente</div>
                                        <div class="fw-semibold" id="txtFacturaCliente">-</div>
                                    </div>
                                </div>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Servicio</th>
                                                <th class="text-center">Cant</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbodyFacturaItems"></tbody>
                                    </table>
                                </div>
                                <div class="d-flex justify-content-end mt-3">
                                    <div class="text-end">
                                        <div class="text-muted small">Total</div>
                                        <div class="h4 mb-0" id="txtFacturaTotal">₡0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="h5 mb-3">Resumen rápido</div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Estado</span>
                                    <span class="fw-semibold" id="txtResumenEstado">-</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Servicios</span>
                                    <span class="fw-semibold" id="txtResumenCantidad">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total</span>
                                    <span class="fw-semibold" id="txtResumenTotal">₡0</span>
                                </div>
                                <hr>
                                <div class="small text-muted">
                                    <?php if (patitas_es_staff()) : ?>
                                    Si el estado está pendiente, revisa el registro de pagos vinculado a esta factura.
                                    <?php else : ?>
                                    Si el estado está pendiente, el personal de la clínica registrará el pago cuando corresponda.
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/facturacion.js?v=4"></script>
</body>
</html>
