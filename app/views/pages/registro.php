<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css?v=9">
</head>
<body class="auth-page">
    <div class="auth-shell auth-shell--wide">
        <a href="index.php?r=home" class="auth-back">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Volver al inicio
        </a>

        <main class="auth-container auth-container--wide">
            <section class="auth-card card auth-card--entrance">
                <div class="card-body">
                    <header class="auth-header mb-3">
                        <div class="auth-brand-row">
                            <div class="auth-logo-badge" aria-hidden="true">🐾</div>
                            <div>
                                <p class="auth-eyebrow mb-0">Nueva cuenta</p>
                                <h1 class="auth-title mb-1">Crear cuenta</h1>
                            </div>
                        </div>
                        <p class="auth-subtitle mb-0">Completa tus datos para agendar citas y ver el historial de tus mascotas.</p>
                    </header>

                    <div id="registro-alert" class="alert d-none auth-alert-slot" role="alert" aria-live="polite"></div>

                    <form id="registro-form" novalidate>
                        <h2 class="auth-form-section">Datos personales</h2>

                        <div class="auth-field auth-field--with-icon">
                            <label for="registro-nombre" class="form-label">Nombre completo</label>
                            <div class="auth-field-control">
                                <div class="auth-input-wrap">
                                    <i class="bi bi-person auth-field__icon" aria-hidden="true"></i>
                                    <input type="text" class="form-control" id="registro-nombre" placeholder="Ej. María Pérez González" maxlength="150" autocomplete="name" aria-required="true">
                                </div>
                                <p class="auth-form-hint mb-0">Nombre y al menos un apellido, solo letras.</p>
                                <div class="auth-field-messages">
                                    <div id="registro-nombre-error" class="invalid-feedback" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field auth-field--with-icon">
                            <label for="registro-cedula" class="form-label">Cédula</label>
                            <div class="auth-field-control">
                                <div class="auth-input-wrap">
                                    <i class="bi bi-card-text auth-field__icon" aria-hidden="true"></i>
                                    <input type="text" class="form-control" id="registro-cedula" placeholder="9 o 10 dígitos" maxlength="20" inputmode="numeric" autocomplete="off" aria-required="true">
                                </div>
                                <p class="auth-form-hint mb-0">Sin guiones; física 9 dígitos o jurídica 10.</p>
                                <div class="auth-field-messages">
                                    <div id="registro-cedula-error" class="invalid-feedback" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field auth-field--with-icon">
                            <label for="registro-telefono" class="form-label">Teléfono</label>
                            <div class="auth-field-control">
                                <div class="auth-input-wrap">
                                    <i class="bi bi-telephone auth-field__icon" aria-hidden="true"></i>
                                    <input type="tel" class="form-control" id="registro-telefono" placeholder="8888-8888 o +506…" maxlength="20" inputmode="tel" autocomplete="tel" aria-required="true">
                                </div>
                                <p class="auth-form-hint mb-0">Incluye código 506 si lo usas; guardamos el número sin espacios.</p>
                                <div class="auth-field-messages">
                                    <div id="registro-telefono-error" class="invalid-feedback" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <h2 class="auth-form-section">Cuenta</h2>

                        <div class="auth-field auth-field--with-icon">
                            <label for="registro-email" class="form-label">Correo electrónico</label>
                            <div class="auth-field-control">
                                <div class="auth-input-wrap">
                                    <i class="bi bi-envelope auth-field__icon" aria-hidden="true"></i>
                                    <input type="email" class="form-control" id="registro-email" placeholder="nombre@ejemplo.com" maxlength="254" autocomplete="email" aria-required="true">
                                </div>
                                <p class="auth-form-hint mb-0">Será tu usuario para iniciar sesión.</p>
                                <div class="auth-field-messages">
                                    <div id="registro-email-error" class="invalid-feedback" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field auth-field--with-icon auth-field--password">
                            <label for="registro-password" class="form-label">Contraseña</label>
                            <div class="auth-field-control auth-password-row">
                                <div class="auth-password-row__input">
                                    <div class="auth-password-input-wrap">
                                        <i class="bi bi-lock auth-field__icon" aria-hidden="true"></i>
                                        <input type="password" class="form-control auth-password-input" id="registro-password" placeholder="Mínimo 4 caracteres" maxlength="200" autocomplete="new-password" aria-required="true">
                                    </div>
                                    <div class="auth-field-messages">
                                        <div id="registro-password-error" class="invalid-feedback" role="alert"></div>
                                    </div>
                                </div>
                                <button type="button" class="auth-password-toggle-side" id="registro-password-toggle" aria-label="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="auth-field auth-field--with-icon auth-field--password">
                            <label for="registro-password-confirm" class="form-label">Confirmar contraseña</label>
                            <div class="auth-field-control auth-password-row">
                                <div class="auth-password-row__input">
                                    <div class="auth-password-input-wrap">
                                        <i class="bi bi-lock-fill auth-field__icon" aria-hidden="true"></i>
                                        <input type="password" class="form-control auth-password-input" id="registro-password-confirm" placeholder="Repite la contraseña" maxlength="200" autocomplete="new-password" aria-required="true">
                                    </div>
                                    <div class="auth-field-messages">
                                        <div id="registro-password-confirm-error" class="invalid-feedback" role="alert"></div>
                                    </div>
                                </div>
                                <button type="button" class="auth-password-toggle-side" id="registro-password-confirm-toggle" aria-label="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 mt-2 mb-0">
                            Crear mi cuenta
                        </button>
                    </form>

                    <footer class="auth-footer">
                        <nav class="auth-links" aria-label="Enlaces de autenticación">
                            <p class="small mb-0">
                                ¿Ya tienes cuenta?
                                <a href="index.php?r=login" class="text-decoration-none fw-semibold">Iniciar sesión</a>
                            </p>
                        </nav>
                    </footer>
                </div>
            </section>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="public/js/rutas.js?v=2"></script>
    <script src="public/js/api.js?v=3"></script>
    <script src="public/js/validaciones.js?v=4"></script>
    <script src="public/js/autenticacion.js?v=7"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => Auth.initRegistro());
    </script>
</body>
</html>
