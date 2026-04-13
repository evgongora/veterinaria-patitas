<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria Patitas - Gestión veterinaria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>

<body class="index-page">
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm patitas-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 fw-bold patitas-focus-ring rounded px-1" href="index.php?r=home">
                <span class="patitas-brand-mark d-inline-flex border-0" style="width:2rem;height:2rem;" aria-hidden="true"><i class="bi bi-heart-pulse-fill"></i></span>
                <span class="text-dark">Veterinaria Patitas</span>
            </a>
            <div class="d-flex gap-2">
                <a href="index.php?r=login" class="btn btn-outline-success rounded-pill px-4">Iniciar sesión</a>
                <a href="index.php?r=registro" class="btn btn-success rounded-pill px-4 shadow-sm">Crear cuenta</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="hero-section py-5 patitas-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <h1 class="display-5 fw-bold text-dark mb-3 patitas-hero-title">
                            Cuidado veterinario <span class="text-success">organizado</span> en un solo sistema
                        </h1>
                        <p class="lead text-secondary mb-4 patitas-hero-lead">
                            Agenda citas en línea, consulta el historial clínico de tus mascotas y revisa facturación e inventario con datos reales desde la base de datos.
                        </p>
                        <div class="patitas-hero-cta d-flex flex-wrap gap-3 align-items-center">
                            <a href="index.php?r=registro" class="btn btn-success btn-lg px-5 rounded-pill btn-patitas-shine patitas-focus-ring">Crear cuenta gratis</a>
                            <a href="index.php?r=login" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Ya tengo cuenta</a>
                        </div>
                    </div>
                    <div class="col-lg-6 patitas-hero-img-wrap text-center text-lg-end">
                        <div class="hero-image rounded-4 overflow-hidden patitas-hero-img d-inline-block max-w-100">
                            <img src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=600&h=400&fit=crop" alt="Atención veterinaria a mascotas" class="img-fluid w-100" width="600" height="400" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="servicios-section py-5">
            <div class="container">
                <h2 class="text-center fw-bold text-dark mb-2">Por qué usar Patitas</h2>
                <p class="text-center text-muted mb-5 mx-auto" style="max-width: 36rem;">Funciones pensadas para dueños de mascotas y para el equipo clínico, sin datos de demostración en pantallas internas.</p>
                <div class="row g-4 patitas-stagger justify-content-center">
                    <div class="col-md-6 col-lg-3">
                        <div class="card servicio-card h-100 border-0 shadow-sm patitas-pillar">
                            <div class="card-body p-4 text-center text-md-start">
                                <div class="servicio-icon rounded-circle mb-3 mx-auto mx-md-0 d-inline-flex align-items-center justify-content-center"><i class="bi bi-calendar-plus" aria-hidden="true"></i></div>
                                <h3 class="h5 fw-bold text-dark mb-2">Citas en línea</h3>
                                <p class="text-muted small mb-0">Elige fecha, veterinario y tipo de consulta; la cita queda registrada en el sistema.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card servicio-card h-100 border-0 shadow-sm patitas-pillar">
                            <div class="card-body p-4 text-center text-md-start">
                                <div class="servicio-icon rounded-circle mb-3 mx-auto mx-md-0 d-inline-flex align-items-center justify-content-center"><i class="bi bi-journal-medical" aria-hidden="true"></i></div>
                                <h3 class="h5 fw-bold text-dark mb-2">Historial clínico</h3>
                                <p class="text-muted small mb-0">Consulta diagnósticos y tratamientos por animal, con datos desde la base de datos.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card servicio-card h-100 border-0 shadow-sm patitas-pillar">
                            <div class="card-body p-4 text-center text-md-start">
                                <div class="servicio-icon rounded-circle mb-3 mx-auto mx-md-0 d-inline-flex align-items-center justify-content-center"><i class="bi bi-receipt-cutoff" aria-hidden="true"></i></div>
                                <h3 class="h5 fw-bold text-dark mb-2">Facturación clara</h3>
                                <p class="text-muted small mb-0">Revisa facturas y pagos asociados a tus citas, con estados actualizados.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card servicio-card h-100 border-0 shadow-sm patitas-pillar">
                            <div class="card-body p-4 text-center text-md-start">
                                <div class="servicio-icon rounded-circle mb-3 mx-auto mx-md-0 d-inline-flex align-items-center justify-content-center"><i class="bi bi-shield-lock" aria-hidden="true"></i></div>
                                <h3 class="h5 fw-bold text-dark mb-2">Acceso seguro</h3>
                                <p class="text-muted small mb-0">Inicio de sesión con sesión en servidor; cada cliente ve solo su información.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer-index py-4 mt-5">
        <div class="container text-center">
            <p class="mb-1 small text-white-50">SC502 · Proyecto Administración Web Cliente/Servidor</p>
            <p class="mb-0 small">&copy; 2026 Veterinaria Patitas</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
