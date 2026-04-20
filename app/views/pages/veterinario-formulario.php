<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Veterinario - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout admin-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel-admin'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'gestion-usuarios'; require __DIR__ . '/../partials/sidebar-admin.php'; ?>
            <main class="app-content">
                <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?r=veterinarios">Veterinarios</a></li><li class="breadcrumb-item active" id="breadcrumbVetAccion">Nuevo veterinario</li></ol></nav>
                <h1 class="h3 fw-bold mb-1" id="tituloVeterinarioForm">Nuevo veterinario</h1>
                <p class="text-muted mb-4">Los datos se guardan en MySQL (tablas USUARIO y VETERINARIO).</p>
                <div id="veterinario-form-alert" class="alert d-none" role="alert"></div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="veterinario-form" novalidate>
                            <div class="row g-3">
                                <div class="col-12 col-md-6"><label for="veterinario-nombre" class="form-label">Nombre completo</label><input type="text" class="form-control" id="veterinario-nombre" required autocomplete="name"><div id="veterinario-nombre-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="veterinario-cedula" class="form-label">Cédula</label><input type="text" class="form-control" id="veterinario-cedula" required autocomplete="off"><div id="veterinario-cedula-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="veterinario-especialidad" class="form-label">Especialidad</label><select class="form-select" id="veterinario-especialidad" required><option value="">Seleccione...</option><option value="General">Medicina General</option><option value="Cirugía">Cirugía</option><option value="Exóticos">Animales Exóticos</option><option value="Ganado">Medicina de Ganado</option></select><div id="veterinario-especialidad-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="veterinario-telefono" class="form-label">Teléfono</label><input type="tel" class="form-control" id="veterinario-telefono" required autocomplete="tel"><div id="veterinario-telefono-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6"><label for="veterinario-email" class="form-label">Correo electrónico</label><input type="email" class="form-control" id="veterinario-email" required autocomplete="email"><div id="veterinario-email-error" class="invalid-feedback"></div></div>
                                <div class="col-12 col-md-6">
                                    <label for="veterinario-password" class="form-label">Contraseña de acceso</label>
                                    <input type="password" class="form-control" id="veterinario-password" minlength="4" autocomplete="new-password">
                                    <div class="form-text" id="veterinario-password-hint">Mínimo 4 caracteres. El veterinario la usará para iniciar sesión.</div>
                                    <div id="veterinario-password-error" class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4"><button type="submit" class="btn btn-success">Guardar</button><a href="index.php?r=veterinarios" class="btn btn-outline-secondary">Cancelar</a></div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/validaciones.js"></script>
    <script src="public/js/veterinarios.js?v=3"></script>
    <script>document.addEventListener('DOMContentLoaded', () => Veterinarios.initFormulario());</script>
</body>
</html>
