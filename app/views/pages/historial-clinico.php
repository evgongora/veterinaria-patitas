<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Clínico - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'historial-clinico'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-3">Historial clínico</h1>

                <div class="mb-4">
                    <label class="form-label" for="selAnimal">Selecciona el animal</label>
                    <select id="selAnimal" class="form-select" style="max-width:320px;">
                        <option value="">Cargando…</option>
                    </select>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Tipo de cita</th>
                                        <th>Notas del veterinario</th>
                                        <th>Veterinario</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyHistorial"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/historial.js?v=2"></script>
</body>
</html>
