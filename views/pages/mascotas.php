<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Animales - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="app-layout">
        <header class="app-header">
            <a href="index.php?r=panel" class="brand"><span class="paw">🐾</span><span>Veterinaria Patitas</span></a>
            <div class="user-area">
                <div class="user-info"><div class="name">Juan Pérez</div><div class="role">Cliente</div></div>
                <a href="index.php?r=login" class="btn-logout">→ Cerrar Sesión</a>
            </div>
        </header>
        <div class="app-body">
            <aside class="app-sidebar">
                <a href="index.php?r=panel" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
                <a href="index.php?r=mascotas" class="nav-item active"><span class="icon">🐾</span> Mis Animales</a>
                <a href="index.php?r=cita-formulario" class="nav-item"><span class="icon">📅</span> Agendar Cita</a>
                <a href="index.php?r=servicios" class="nav-item"><span class="icon">🩺</span> Servicios</a>
                <a href="index.php?r=historial-clinico" class="nav-item"><span class="icon">📋</span> Historial Clínico</a>
                <a href="index.php?r=facturas" class="nav-item"><span class="icon">🧾</span> Facturas</a>
            </aside>
            <main class="app-content">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Mis Animales</h1>
                        <p class="text-muted mb-0">Gestiona la información de tus mascotas</p>
                    </div>
                    <a href="index.php?r=mascota-formulario" class="btn btn-success">+ Registrar Animal</a>
                </div>

                <div class="row g-4" id="contenedor-mascotas">
                    <article class="col-12 col-lg-4">
                        <div class="card pet-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="pet-avatar">🐕</div>
                                    <div class="d-flex gap-2">
                                        <a href="index.php?r=mascota-formulario&id=1" class="btn btn-sm btn-link text-secondary p-0">✏️</a>
                                        <button type="button" class="btn btn-sm btn-link text-secondary p-0">🗑️</button>
                                    </div>
                                </div>
                                <h3 class="h5 fw-bold mb-1">Max</h3>
                                <p class="text-muted small mb-3">Perro</p>
                                <div class="pet-detail mb-2"><span class="label">Raza:</span><span class="value">Labrador Retriever</span></div>
                                <div class="pet-detail mb-2"><span class="label">Edad:</span><span class="value">3 años</span></div>
                                <div class="pet-detail mb-2"><span class="label">Sexo:</span><span class="value">Macho</span></div>
                                <div class="pet-detail mb-2"><span class="label">Peso:</span><span class="value">28 kg</span></div>
                                <div class="pet-detail mb-3"><span class="label">Color:</span><span class="value">Dorado</span></div>
                                <p class="small mb-3"><strong>Observaciones:</strong> Muy activo y juguetón</p>
                                <a href="index.php?r=historial-clinico&animal=1" class="btn btn-outline-primary w-100">Ver Historial Clínico</a>
                            </div>
                        </div>
                    </article>
                    <article class="col-12 col-lg-4">
                        <div class="card pet-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="pet-avatar">🐱</div>
                                    <div class="d-flex gap-2">
                                        <a href="index.php?r=mascota-formulario&id=2" class="btn btn-sm btn-link text-secondary p-0">✏️</a>
                                        <button type="button" class="btn btn-sm btn-link text-secondary p-0">🗑️</button>
                                    </div>
                                </div>
                                <h3 class="h5 fw-bold mb-1">Luna</h3>
                                <p class="text-muted small mb-3">Gato</p>
                                <div class="pet-detail mb-2"><span class="label">Raza:</span><span class="value">Siamés</span></div>
                                <div class="pet-detail mb-2"><span class="label">Edad:</span><span class="value">2 años</span></div>
                                <div class="pet-detail mb-2"><span class="label">Sexo:</span><span class="value">Hembra</span></div>
                                <div class="pet-detail mb-2"><span class="label">Peso:</span><span class="value">4 kg</span></div>
                                <div class="pet-detail mb-3"><span class="label">Color:</span><span class="value">Crema con puntos marrones</span></div>
                                <p class="small mb-3"><strong>Observaciones:</strong> Alérgica a ciertos alimentos</p>
                                <a href="index.php?r=historial-clinico&animal=2" class="btn btn-outline-primary w-100">Ver Historial Clínico</a>
                            </div>
                        </div>
                    </article>
                    <article class="col-12 col-lg-4">
                        <div class="card pet-card h-100">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="pet-avatar">🐕</div>
                                    <div class="d-flex gap-2">
                                        <a href="index.php?r=mascota-formulario&id=3" class="btn btn-sm btn-link text-secondary p-0">✏️</a>
                                        <button type="button" class="btn btn-sm btn-link text-secondary p-0">🗑️</button>
                                    </div>
                                </div>
                                <h3 class="h5 fw-bold mb-1">Rocky</h3>
                                <p class="text-muted small mb-3">Perro</p>
                                <div class="pet-detail mb-2"><span class="label">Raza:</span><span class="value">Pastor Alemán</span></div>
                                <div class="pet-detail mb-2"><span class="label">Edad:</span><span class="value">5 años</span></div>
                                <div class="pet-detail mb-2"><span class="label">Sexo:</span><span class="value">Macho</span></div>
                                <div class="pet-detail mb-2"><span class="label">Peso:</span><span class="value">35 kg</span></div>
                                <div class="pet-detail mb-3"><span class="label">Color:</span><span class="value">Negro y café</span></div>
                                <p class="small mb-3"><strong>Observaciones:</strong> Necesita ejercicio diario</p>
                                <a href="index.php?r=historial-clinico&animal=3" class="btn btn-outline-primary w-100">Ver Historial Clínico</a>
                            </div>
                        </div>
                    </article>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/rutas.js"></script>
</body>
</html>
