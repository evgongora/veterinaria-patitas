<?php /* Vista factura-detalle — MVC */ ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle de Factura | Veterinaria Patitas</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/estilos.css" />
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
    <div class="container-fluid px-3">
      <button class="btn btn-link text-dark me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral">
        <span class="fs-4">≡</span>
      </button>

      <a class="navbar-brand fw-semibold d-flex align-items-center gap-2" href="index.php?r=panel">
        <span class="fs-5">🐾</span>
        <span style="color:#2E7D32;">Veterinaria Patitas</span>
      </a>

      <div class="ms-auto d-flex align-items-center gap-3">
        <div class="text-end">
          <div class="fw-semibold small" id="txtNombreUsuario">Usuario</div>
          <div class="text-muted small" id="txtRolUsuario">Cliente</div>
        </div>

        <a href="index.php?r=home" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnCerrarSesion">
          <span>⎋</span>
          <span>Cerrar Sesion</span>
        </a>
      </div>
    </div>
  </nav>

  <div class="offcanvas offcanvas-start" tabindex="-1" id="menuLateral">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Menu</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="list-group">
        <a class="list-group-item list-group-item-action" href="index.php?r=panel">Dashboard</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=servicios">Servicios</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=mascotas">Mis Animales</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=citas">Agendar Cita</a>
        <a class="list-group-item list-group-item-action active" href="index.php?r=facturas">Facturas</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=pagos">Pagos</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=inventario">Inventario</a>
        <a class="list-group-item list-group-item-action" href="index.php?r=evaluacion">Evaluacion</a>
      </div>
    </div>
  </div>

  <main class="container py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
      <div>
        <h1 class="h3 mb-1">Detalle de factura</h1>
        <p class="text-muted mb-0">Informacion y desglose de la factura</p>
      </div>

      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="index.php?r=facturas">Volver</a>
        <a class="btn btn-primary" id="btnIrPagos" href="index.php?r=pagos">Ir a pagos</a>
      </div>
    </div>

    <div id="alertaDetalleFactura"></div>

    <div class="row g-3">
      <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0" style="border-radius:18px;">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Factura</div>
                <div class="h4 mb-0" id="txtFacturaId">FAC-000</div>
              </div>
              <div id="badgeEstadoFactura"></div>
            </div>

            <hr />

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

            <hr />

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
        <div class="card shadow-sm border-0" style="border-radius:18px;">
          <div class="card-body">
            <div class="h5 mb-3">Resumen rapido</div>

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

            <hr />

            <div class="small text-muted">
              Si esta pendiente puedes ir a pagos para simular el pago
            </div>

          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>

  <footer>
    <div class="footer">
      <p>SC502 - Proyecto Administracion Web Cliente Servidor</p>
      <p>&copy; 2026 Veterinaria Patitas Sistema de Gestion Veterinaria</p>
    </div>
  </footer>

  <script src="js/facturacion.js"></script>
</body>
</html>