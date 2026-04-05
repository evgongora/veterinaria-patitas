<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citas | Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="container main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="section-title">Citas</h1>
                <p class="section-subtitle">Administra las citas programadas.</p>
            </div>
            <a href="index.php?r=cita-formulario" class="btn btn-secundario">Agendar cita</a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Veterinario</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Tipo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCitas"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/rutas.js"></script>
    <script src="js/validaciones.js"></script>
    <script src="js/citas.js"></script>
</body>
</html>