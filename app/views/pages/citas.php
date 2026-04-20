<?php
declare(strict_types=1);
$patitasRolFk = isset($_SESSION['rol_fk']) ? (int) $_SESSION['rol_fk'] : 0;
$patitasEsStaff = $patitasRolFk === 1 || $patitasRolFk === 2;
$patitasHeaderHome = $patitasEsStaff ? 'index.php?r=panel-admin' : 'index.php?r=panel';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $patitasEsStaff ? 'Listado de citas' : 'Mis citas' ?> | Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body class="patitas-app" data-rol-fk="<?= (int) $patitasRolFk ?>">
    <div class="app-layout<?= $patitasEsStaff ? ' admin-layout' : '' ?>">
        <?php require __DIR__ . '/../partials/app-header.php'; ?>
        <div class="app-body">
            <?php
            $patitasSidebarActive = 'citas';
            if ($patitasEsStaff) {
                require __DIR__ . '/../partials/sidebar-admin.php';
            } else {
                require __DIR__ . '/../partials/sidebar-cliente.php';
            }
            ?>
            <main class="app-content">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1"><?= $patitasEsStaff ? 'Listado de citas' : 'Mis citas' ?></h1>
                        <p class="text-muted small mb-0"><?= $patitasEsStaff ? 'Todas las citas registradas en el sistema.' : 'Consulta el estado de tus citas programadas.' ?></p>
                    </div>
                    <?php if ($patitasEsStaff) : ?>
                        <a href="index.php?r=citas-dia" class="btn btn-outline-success">Citas del día</a>
                    <?php else : ?>
                        <a href="index.php?r=cita-formulario" class="btn btn-success">Agendar cita</a>
                    <?php endif; ?>
                </div>

                <div id="alertCitasLista" class="alert d-none mb-3" role="alert"></div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Veterinario</th>
                                        <th>Fecha</th>
                                        <th>Horario</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th class="text-end"><?= $patitasEsStaff ? 'ID' : 'Acción' ?></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCitas">
                                    <tr><td colspan="7" class="text-muted py-4 text-center">Cargando…</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/confirm-modal.js?v=2"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/rutas.js?v=3"></script>
    <script src="public/js/citas.js?v=10"></script>
</body>
</html>
