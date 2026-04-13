<?php
declare(strict_types=1);
/** @var string $patitasHeaderHome enlace al dashboard (index.php?r=panel o panel-admin) */
if (! isset($patitasHeaderHome) || $patitasHeaderHome === '') {
    $patitasHeaderHome = 'index.php?r=panel';
}
$patitasHeaderHomeEsc = htmlspecialchars($patitasHeaderHome, ENT_QUOTES, 'UTF-8');
?>
<header class="app-header patitas-header">
    <div class="patitas-header-inner">
        <a href="<?php echo $patitasHeaderHomeEsc; ?>" class="brand patitas-brand patitas-focus-ring">
            <span class="patitas-brand-mark" aria-hidden="true"><i class="bi bi-heart-pulse-fill"></i></span>
            <span class="patitas-brand-text">Veterinaria Patitas</span>
        </a>
        <div class="patitas-header-actions">
            <div class="patitas-user-block">
                <div class="patitas-avatar" id="patitasAvatarIniciales" aria-hidden="true">VP</div>
                <div class="patitas-user-info">
                    <div class="patitas-user-name" id="txtNombreUsuario">Usuario</div>
                    <div class="patitas-user-role" id="txtRolUsuario">Cliente</div>
                </div>
            </div>
            <a href="index.php?r=login" class="btn-logout patitas-header-logout" title="Cerrar sesión"><i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i>Cerrar sesión</a>
        </div>
    </div>
</header>
