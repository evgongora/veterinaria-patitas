<?php
declare(strict_types=1);
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No encontrado — Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars(asset('css/estilos.css')); ?>">
</head>
<body class="p-5">
    <h1 class="h3">Página no encontrada</h1>
    <p class="text-muted">La ruta solicitada no existe.</p>
    <a href="<?php echo htmlspecialchars(page('home')); ?>" class="btn btn-success">Volver al inicio</a>
</body>
</html>
