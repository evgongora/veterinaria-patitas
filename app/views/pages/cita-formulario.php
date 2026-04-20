<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app">
    <?php $patitasEditCitaId = isset($_GET['id']) ? (int) $_GET['id'] : 0; ?>
    <div class="app-layout">
        <?php $patitasHeaderHome = 'index.php?r=panel'; require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php $patitasSidebarActive = 'cita-formulario'; require __DIR__ . '/../partials/sidebar-cliente.php'; ?>
            <main class="app-content">
                <h1 class="h3 fw-bold mb-3"><?= $patitasEditCitaId ? 'Editar cita' : 'Agendar cita' ?></h1>
                <div id="alertCita" class="alert d-none" role="alert"></div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form id="formCita">
                            <input type="hidden" id="citaId" value="<?= $patitasEditCitaId > 0 ? (int) $patitasEditCitaId : '' ?>">
                            <div class="mb-4">
                                <label class="form-label">Selecciona el Animal <span class="text-danger">*</span></label>
                                <select id="animal" class="form-select" required>
                                    <option value="">Cargando...</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Veterinario <span class="text-danger">*</span></label>
                                <select id="veterinario" class="form-select" required>
                                    <option value="">Cargando...</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Tipo de cita</label>
                                <select id="tipoCita" class="form-select"></select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                <input type="date" id="fecha" class="form-control" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Horarios disponibles <span class="text-danger">*</span></label>
                                <p class="text-muted small mb-2" id="hintHorariosVet">Tras elegir <strong>fecha</strong> y <strong>veterinario</strong>, verás los huecos libres solo para esa agenda; el mismo horario puede estar libre con otro doctor.</p>
                                <div class="d-flex flex-wrap gap-2" id="horarios">
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="09:00" class="d-none"> 09:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="10:00" class="d-none"> 10:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="11:00" class="d-none"> 11:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="12:00" class="d-none"> 12:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="14:00" class="d-none"> 14:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="15:00" class="d-none"> 15:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="16:00" class="d-none"> 16:00</label>
                                    <label class="btn btn-light border horario-btn"><input type="radio" name="hora" value="17:00" class="d-none"> 17:00</label>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Motivo de la Consulta <span class="text-danger">*</span></label>
                                <textarea id="motivo" class="form-control" rows="3" placeholder="Describe brevemente el motivo de la consulta (opcional)…"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSubmitCita"><?= $patitasEditCitaId ? 'Guardar cambios' : 'Confirmar cita' ?></button>
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
    <script src="public/js/citas.js?v=9"></script>
</body>
</html>
