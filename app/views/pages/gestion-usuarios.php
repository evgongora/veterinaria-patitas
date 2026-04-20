<?php
declare(strict_types=1);
if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . page('login'), true, 302);
    exit;
}
if (! patitas_es_admin()) {
    header('Location: ' . page('panel-admin'), true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de usuarios - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'gestion-usuarios'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-2">Gestión de usuarios</h1>
                <p class="text-muted small mb-4">Administra clientes y veterinarios desde un solo lugar.</p>

                <div class="patitas-segment mb-4" id="patitasSegment" data-seg="0" role="tablist" aria-label="Seleccionar tipo">
                    <span class="patitas-segment-thumb" aria-hidden="true"></span>
                    <button type="button" class="patitas-segment-btn active" id="segBtnClientes" data-tab="clientes" role="tab" aria-selected="true">
                        <i class="bi bi-person-hearts me-1" aria-hidden="true"></i>Clientes
                    </button>
                    <button type="button" class="patitas-segment-btn" id="segBtnVets" data-tab="veterinarios" role="tab" aria-selected="false">
                        <i class="bi bi-heart-pulse me-1" aria-hidden="true"></i>Veterinarios
                    </button>
                </div>

                <!-- Panel clientes -->
                <div id="gu-panel-clientes" class="gu-slide-panel">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 fw-bold mb-0">Clientes</h2>
                        <button type="button" class="btn btn-success btn-sm" id="btnGuToggleClienteForm">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i><span id="btnGuClienteFormLabel">Nuevo cliente</span>
                        </button>
                    </div>
                    <div id="gu-alert-cliente" class="alert d-none" role="alert"></div>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="tabla-gu-clientes">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre</th>
                                            <th>Teléfono</th>
                                            <th>Correo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td colspan="5" class="text-muted py-4 text-center">Cargando…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="collapseFormCliente">
                        <div class="card border-0 shadow-sm border-top patitas-form-card">
                            <div class="card-body p-4">
                                <h3 class="h6 fw-bold mb-3" id="guClienteTituloForm">Nuevo cliente</h3>
                                <form id="gu-cliente-form" novalidate>
                                    <input type="hidden" id="gu-cliente-edit-id" value="">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="gu-cliente-nombre" class="form-label">Nombre completo</label>
                                            <input type="text" class="form-control" id="gu-cliente-nombre" required autocomplete="name">
                                            <div id="gu-cliente-nombre-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-cliente-cedula" class="form-label">Cédula</label>
                                            <input type="text" class="form-control" id="gu-cliente-cedula" required autocomplete="off">
                                            <div id="gu-cliente-cedula-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-cliente-telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="gu-cliente-telefono" required autocomplete="tel">
                                            <div id="gu-cliente-telefono-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-cliente-email" class="form-label">Correo electrónico</label>
                                            <input type="email" class="form-control" id="gu-cliente-email" required autocomplete="email">
                                            <div id="gu-cliente-email-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6" id="gu-cliente-password-row">
                                            <label for="gu-cliente-password" class="form-label">Contraseña de acceso</label>
                                            <input type="password" class="form-control" id="gu-cliente-password" minlength="4" autocomplete="new-password">
                                            <div class="form-text" id="gu-cliente-password-hint">Mínimo 4 caracteres para nuevos registros.</div>
                                            <div id="gu-cliente-password-error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        <button type="submit" class="btn btn-success">Guardar</button>
                                        <button type="button" class="btn btn-outline-secondary" id="gu-cliente-cancelar">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel veterinarios -->
                <div id="gu-panel-veterinarios" class="gu-slide-panel d-none">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                        <h2 class="h5 fw-bold mb-0">Veterinarios</h2>
                        <button type="button" class="btn btn-primary btn-sm" id="btnGuToggleVetForm">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i><span id="btnGuVetFormLabel">Nuevo veterinario</span>
                        </button>
                    </div>
                    <div id="gu-alert-vet" class="alert d-none" role="alert"></div>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover mb-0" id="tabla-gu-veterinarios">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Especialidad</th>
                                            <th>Teléfono</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td colspan="5" class="text-muted py-4 text-center">Cargando…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="collapseFormVet">
                        <div class="card border-0 shadow-sm border-top patitas-form-card">
                            <div class="card-body p-4">
                                <h3 class="h6 fw-bold mb-3" id="guVetTituloForm">Nuevo veterinario</h3>
                                <form id="gu-vet-form" novalidate>
                                    <input type="hidden" id="gu-vet-edit-id" value="">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-nombre" class="form-label">Nombre completo</label>
                                            <input type="text" class="form-control" id="gu-vet-nombre" required autocomplete="name">
                                            <div id="gu-vet-nombre-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-cedula" class="form-label">Cédula</label>
                                            <input type="text" class="form-control" id="gu-vet-cedula" required autocomplete="off">
                                            <div id="gu-vet-cedula-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-especialidad" class="form-label">Especialidad</label>
                                            <select class="form-select" id="gu-vet-especialidad" required>
                                                <option value="">Seleccione…</option>
                                                <option value="General">Medicina general</option>
                                                <option value="Cirugía">Cirugía</option>
                                                <option value="Exóticos">Animales exóticos</option>
                                                <option value="Ganado">Medicina de ganado</option>
                                            </select>
                                            <div id="gu-vet-especialidad-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-telefono" class="form-label">Teléfono</label>
                                            <input type="tel" class="form-control" id="gu-vet-telefono" required autocomplete="tel">
                                            <div id="gu-vet-telefono-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-email" class="form-label">Correo electrónico</label>
                                            <input type="email" class="form-control" id="gu-vet-email" required autocomplete="email">
                                            <div id="gu-vet-email-error" class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label for="gu-vet-password" class="form-label">Contraseña de acceso</label>
                                            <input type="password" class="form-control" id="gu-vet-password" minlength="4" autocomplete="new-password">
                                            <div class="form-text" id="gu-vet-password-hint">Mínimo 4 caracteres para nuevos registros.</div>
                                            <div id="gu-vet-password-error" class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <button type="button" class="btn btn-outline-secondary" id="gu-vet-cancelar">Cancelar</button>
                                    </div>
                                </form>
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
    <script src="public/js/validaciones.js"></script>
    <script src="public/js/gestion-usuarios.js?v=3"></script>
</body>
</html>
