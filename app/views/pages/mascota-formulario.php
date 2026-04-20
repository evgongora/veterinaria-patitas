<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Animal - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'mascotas'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-3">Registrar animal</h1>
                <div id="alertMascota" class="alert d-none" role="alert"></div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="formMascota">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" id="nombre" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Especie / Raza</label>
                                    <select id="razaId" class="form-select" required>
                                        <option value="">Cargando razas...</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Edad (años)</label>
                                    <input type="number" id="edad" class="form-control" min="0">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Sexo</label>
                                    <select id="sexo" class="form-select">
                                        <option value="">Seleccione</option>
                                        <option value="Macho">Macho</option>
                                        <option value="Hembra">Hembra</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Peso (kg)</label>
                                    <input type="number" step="0.1" id="peso" class="form-control" min="0">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Observaciones</label>
                                    <textarea id="observaciones" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="mt-4 d-flex gap-2">
                                <button type="submit" class="btn btn-success">Guardar</button>
                                <a href="index.php?r=mascotas" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/mascotas.js?v=4"></script>
</body>
</html>
