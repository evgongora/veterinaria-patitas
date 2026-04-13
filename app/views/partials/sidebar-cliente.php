<?php
declare(strict_types=1);
$patitasSidebarActive = $patitasSidebarActive ?? '';
$na = function (string $route) use ($patitasSidebarActive): string {
    return $route === $patitasSidebarActive ? ' active' : '';
};
?>
<aside class="app-sidebar">
    <a href="index.php?r=panel" class="nav-item<?php echo $na('panel'); ?>"><i class="bi bi-grid-1x2-fill patitas-nav-icon" aria-hidden="true"></i> Dashboard</a>
    <a href="index.php?r=mascotas" class="nav-item<?php echo $na('mascotas'); ?>"><i class="bi bi-heart-fill patitas-nav-icon" aria-hidden="true"></i> Mis animales</a>
    <a href="index.php?r=cita-formulario" class="nav-item<?php echo $na('cita-formulario'); ?>"><i class="bi bi-calendar-plus patitas-nav-icon" aria-hidden="true"></i> Agendar cita</a>
    <a href="index.php?r=citas" class="nav-item<?php echo $na('citas'); ?>"><i class="bi bi-calendar-check patitas-nav-icon" aria-hidden="true"></i> Mis citas</a>
    <a href="index.php?r=servicios" class="nav-item<?php echo $na('servicios'); ?>"><i class="bi bi-heart-pulse patitas-nav-icon" aria-hidden="true"></i> Servicios</a>
    <a href="index.php?r=historial-clinico" class="nav-item<?php echo $na('historial-clinico'); ?>"><i class="bi bi-journal-medical patitas-nav-icon" aria-hidden="true"></i> Historial clínico</a>
    <a href="index.php?r=facturas" class="nav-item<?php echo $na('facturas'); ?>"><i class="bi bi-receipt-cutoff patitas-nav-icon" aria-hidden="true"></i> Facturas</a>
    <a href="index.php?r=pagos" class="nav-item<?php echo $na('pagos'); ?>"><i class="bi bi-credit-card patitas-nav-icon" aria-hidden="true"></i> Pagos</a>
    <a href="index.php?r=evaluacion" class="nav-item<?php echo $na('evaluacion'); ?>"><i class="bi bi-star-fill patitas-nav-icon" aria-hidden="true"></i> Evaluación</a>
</aside>
