<?php
declare(strict_types=1);
$patitasRolFk = isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0;
$patitasEsStaff = $patitasRolFk === 1 || $patitasRolFk === 2;
$patitasEsCliente = $patitasRolFk === 3;
$patitasHeaderHome = $patitasEsStaff ? 'index.php?r=panel-admin' : 'index.php?r=panel';
$patitasCancelar = $patitasEsStaff ? 'index.php?r=panel-admin' : 'index.php?r=panel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluaciones - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app" data-rol-fk="<?php echo $patitasRolFk; ?>">
    <div class="app-layout<?php echo $patitasEsStaff ? ' admin-layout' : ''; ?>">
        <?php require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php
            $patitasSidebarActive = 'evaluacion';
            if ($patitasEsStaff) {
                require __DIR__ . '/../partials/sidebar-admin.php';
            } else {
                require __DIR__ . '/../partials/sidebar-cliente.php';
            }
            ?>
            <main class="app-content">
                <div id="alertaEvaluacion"></div>

                <!-- Personal: lectura de todas las evaluaciones -->
                <div id="panelEvalStaff" class="d-none">
                    <h1 class="h3 fw-bold mb-3">Valoraciones de clientes</h1>
                    <p class="text-muted small mb-3">Lectura de todas las opiniones registradas.</p>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Cliente / usuario</th>
                                            <th>Correo</th>
                                            <th class="text-center">Puntos</th>
                                            <th>Comentario</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbodyEvalStaff">
                                        <tr><td colspan="5" class="text-muted py-4 text-center">Cargando…</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cliente: feed + propias + formulario -->
                <div id="panelEvalCliente" class="d-none">
                    <h1 class="h3 fw-bold mb-3">Evaluaciones</h1>

                    <h2 class="h6 fw-bold mb-2">Opiniones de la comunidad</h2>
                    <div id="listaEvaluacionesPub" class="evaluaciones-lista mb-4">
                        <p class="text-muted small mb-0">Cargando…</p>
                    </div>

                    <h2 class="h6 fw-bold mb-2">Mis evaluaciones</h2>
                    <div id="listaMisEvaluaciones" class="mb-4">
                        <p class="text-muted small mb-0">Cargando…</p>
                    </div>

                    <div id="alertaSesionEval" class="mb-3" hidden></div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h6 fw-bold mb-3" id="tituloFormEval">Nueva evaluación</h2>
                            <form id="formEvaluacion">
                                <input type="hidden" id="evalId" value="">
                                <div class="mb-3">
                                    <label class="form-label mb-2">Puntuación</label>
                                    <div class="d-flex gap-2 align-items-center flex-wrap" id="grupoEstrellas">
                                        <input class="btn-check" type="radio" name="rating" id="star1" value="1">
                                        <label class="btn btn-outline-warning" for="star1">★</label>
                                        <input class="btn-check" type="radio" name="rating" id="star2" value="2">
                                        <label class="btn btn-outline-warning" for="star2">★★</label>
                                        <input class="btn-check" type="radio" name="rating" id="star3" value="3">
                                        <label class="btn btn-outline-warning" for="star3">★★★</label>
                                        <input class="btn-check" type="radio" name="rating" id="star4" value="4">
                                        <label class="btn btn-outline-warning" for="star4">★★★★</label>
                                        <input class="btn-check" type="radio" name="rating" id="star5" value="5">
                                        <label class="btn btn-outline-warning" for="star5">★★★★★</label>
                                    </div>
                                    <div class="form-text" id="txtRatingAyuda">Selecciona de 1 a 5</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="comentario">Comentario</label>
                                    <textarea class="form-control" id="comentario" rows="4" placeholder="Qué te gustó o qué mejorarías"></textarea>
                                </div>
                                <div class="row g-2">
                                    <div class="col-12 col-md-4">
                                        <button type="button" class="btn btn-outline-secondary w-100 d-none" id="btnCancelarEdicionEval">Cancelar edición</button>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <a class="btn btn-outline-secondary w-100" href="<?php echo htmlspecialchars($patitasCancelar, ENT_QUOTES, 'UTF-8'); ?>">Volver</a>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <button class="btn btn-success w-100" type="submit" id="btnSubmitEval">Enviar evaluación</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Invitado: solo lectura del feed -->
                <div id="panelEvalAnon" class="d-none">
                    <h1 class="h3 fw-bold mb-3">Evaluaciones</h1>
                    <p class="text-muted small mb-3">Opiniones de clientes (autor anonimizado).</p>
                    <div id="listaEvaluacionesAnon" class="evaluaciones-lista mb-3">
                        <p class="text-muted small mb-0">Cargando…</p>
                    </div>
                    <div class="alert alert-info mb-0">
                        <a href="index.php?r=login" class="alert-link">Inicia sesión como cliente</a> para publicar o gestionar tus evaluaciones.
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=2"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/evaluaciones.js?v=5"></script>
</body>
</html>
