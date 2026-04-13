<?php
declare(strict_types=1);
$patitasSidebarActive = $patitasSidebarActive ?? '';
$na = function (string $route) use ($patitasSidebarActive): string {
    return $route === $patitasSidebarActive ? ' active' : '';
};
?>
<aside class="app-sidebar">
    <a href="index.php?r=panel-admin" class="nav-item<?php echo $na('panel-admin'); ?>"><i class="bi bi-grid-1x2-fill patitas-nav-icon" aria-hidden="true"></i> Dashboard</a>
    <?php if (patitas_es_admin()) : ?>
    <a href="index.php?r=gestion-usuarios" class="nav-item<?php echo $na('gestion-usuarios'); ?>"><i class="bi bi-people-fill patitas-nav-icon" aria-hidden="true"></i> Gestión de usuarios</a>
    <?php endif; ?>
    <a href="index.php?r=citas-dia" class="nav-item<?php echo $na('citas-dia'); ?>"><i class="bi bi-calendar-day patitas-nav-icon" aria-hidden="true"></i> Citas del día</a>
    <a href="index.php?r=servicios-admin" class="nav-item<?php echo $na('servicios-admin'); ?>"><i class="bi bi-heart-pulse patitas-nav-icon" aria-hidden="true"></i> Servicios</a>
    <a href="index.php?r=inventario" class="nav-item<?php echo $na('inventario'); ?>"><i class="bi bi-box-seam patitas-nav-icon" aria-hidden="true"></i> Inventario</a>
    <a href="index.php?r=reportes" class="nav-item<?php echo $na('reportes'); ?>"><i class="bi bi-graph-up-arrow patitas-nav-icon" aria-hidden="true"></i> Reportes</a>
    <a href="index.php?r=evaluacion" class="nav-item<?php echo $na('evaluacion'); ?>"><i class="bi bi-star patitas-nav-icon" aria-hidden="true"></i> Evaluaciones</a>
</aside>
