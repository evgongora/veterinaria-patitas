<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Veterinaria Patitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/estilos.css?v=9">
</head>
<body class="auth-page">
    <div class="auth-shell">
        <a href="index.php?r=home" class="auth-back">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            Volver al inicio
        </a>

        <main class="auth-container">
            <section class="auth-card card auth-card--entrance">
                <div class="card-body">
                    <header class="auth-header mb-4">
                        <div class="auth-brand-row">
                            <div class="auth-logo-badge" aria-hidden="true">🐾</div>
                            <div>
                                <p class="auth-eyebrow mb-0">Acceso</p>
                                <h1 class="auth-title mb-1">Iniciar sesión</h1>
                            </div>
                        </div>
                        <p class="auth-subtitle mb-0">Entra con el correo y la contraseña de tu cuenta Patitas.</p>
                    </header>

                    <div id="login-alert" class="alert alert-danger d-none auth-alert-slot" role="alert" aria-live="polite"></div>

                    <form id="login-form" novalidate>
                        <div class="auth-field auth-field--with-icon">
                            <label for="login-email" class="form-label">Correo electrónico</label>
                            <div class="auth-field-control">
                                <div class="auth-input-wrap">
                                    <i class="bi bi-envelope auth-field__icon" aria-hidden="true"></i>
                                    <input type="email" class="form-control" id="login-email" placeholder="nombre@ejemplo.com" maxlength="254" autocomplete="username" aria-required="true">
                                </div>
                                <div class="auth-field-messages">
                                    <div id="login-email-error" class="invalid-feedback" role="alert"></div>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field auth-field--with-icon auth-field--password">
                            <label for="login-password" class="form-label">Contraseña</label>
                            <div class="auth-field-control auth-password-row">
                                <div class="auth-password-row__input">
                                    <div class="auth-password-input-wrap">
                                        <i class="bi bi-lock auth-field__icon" aria-hidden="true"></i>
                                        <input type="password" class="form-control auth-password-input" id="login-password" maxlength="200" autocomplete="current-password" aria-required="true">
                                    </div>
                                    <div class="auth-field-messages">
                                        <div id="login-password-error" class="invalid-feedback" role="alert"></div>
                                    </div>
                                </div>
                                <button type="button" class="auth-password-toggle-side" id="login-password-toggle" aria-label="Mostrar contraseña" aria-pressed="false">
                                    <i class="bi bi-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 mt-2 mb-0">
                            Entrar
                        </button>
                    </form>

                    <footer class="auth-footer">
                        <p class="auth-footer-note mb-2">¿Primera vez aquí? Crea tu cuenta en un minuto.</p>
                        <nav class="auth-links" aria-label="Enlaces de autenticación">
                            <p class="small mb-0">
                                ¿No tienes cuenta?
                                <a href="index.php?r=registro" class="text-decoration-none fw-semibold">Registrarse</a>
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
        document.addEventListener('DOMContentLoaded', () => Auth.initLogin());
    </script>
</body>
</html>
