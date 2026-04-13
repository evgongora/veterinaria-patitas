<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Cliente - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'gestion-usuarios'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?r=clientes">Clientes</a></li><li class="breadcrumb-item active" id="breadcrumbClienteAccion">Nuevo cliente</li></ol></nav>
                <h1 class="h3 fw-bold mb-1" id="tituloClienteForm">Nuevo cliente</h1>
                <p class="text-muted mb-4">Los datos se guardan en MySQL (tablas USUARIO y CLIENTE).</p>
                <div id="cliente-form-alert" class="alert d-none" role="alert"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="cliente-form" novalidate>
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label for="cliente-nombre" class="form-label">Nombre completo</label><input type="text" class="form-control" id="cliente-nombre" required autocomplete="name"><div id="cliente-nombre-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="cliente-cedula" class="form-label">Cédula</label><input type="text" class="form-control" id="cliente-cedula" required autocomplete="off"><div id="cliente-cedula-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="cliente-telefono" class="form-label">Teléfono</label><input type="tel" class="form-control" id="cliente-telefono" required autocomplete="tel"><div id="cliente-telefono-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="cliente-email" class="form-label">Correo electrónico</label><input type="email" class="form-control" id="cliente-email" required autocomplete="email"><div id="cliente-email-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6" id="cliente-password-row">
                                    <label for="cliente-password" class="form-label">Contraseña de acceso</label>
                                    <input type="password" class="form-control" id="cliente-password" minlength="4" autocomplete="new-password">
                                    <div class="form-text" id="cliente-password-hint">Mínimo 4 caracteres. El cliente la usará para iniciar sesión.</div>
                                    <div id="cliente-password-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-success">Guardar</button><a href="index.php?r=clientes" class="btn btn-outline-secondary">Cancelar</a></div>
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
    <script src="public/js/clientes.js?v=3"></script>
    <script>document.addEventListener('DOMContentLoaded', () => Clientes.initFormulario());</script>
</body>
</html>
