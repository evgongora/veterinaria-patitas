/**
 * Veterinaria Patitas - Autenticación
 * Login / registro vía api.php?route=…
 */

function authRedirect(route) {
  const rel = 'index.php?r=' + encodeURIComponent(route);
  try {
    return new URL(rel, window.location.href).href;
  } catch (e) {
    return rel;
  }
}

const Auth = {
  bindPasswordToggle(buttonId, inputId) {
    const btn = document.getElementById(buttonId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;
    const icon = btn.querySelector('i');
    btn.addEventListener('click', () => {
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
      btn.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
      if (icon) {
        icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
      }
    });
  },

  initLogin() {
    const form = document.getElementById('login-form');
    const alertEl = document.getElementById('login-alert');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');

    if (!form) return;

    this.bindPasswordToggle('login-password-toggle', 'login-password');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const email = (emailInput.value || '').trim().toLowerCase();
      const password = passwordInput.value || '';

      const emailVal = Validaciones.email(email);
      const passwordVal = Validaciones.requerido(password, 'La contraseña');
      const passMax = Validaciones.longitudMaxima(password, 200, 'La contraseña');

      let hayError = false;

      if (!emailVal.valido) {
        this.mostrarErrorCampo(emailInput, emailVal.mensaje);
        hayError = true;
      } else {
        this.limpiarErrorCampo(emailInput);
      }

      if (!passwordVal.valido) {
        this.mostrarErrorCampo(passwordInput, passwordVal.mensaje);
        hayError = true;
      } else if (!passMax.valido) {
        this.mostrarErrorCampo(passwordInput, passMax.mensaje);
        hayError = true;
      } else {
        this.limpiarErrorCampo(passwordInput);
      }

      if (hayError) return;

      if (typeof apiPostJson !== 'function') {
        this.mostrarAlerta(alertEl, 'No se pudo cargar el cliente API.', 'danger');
        return;
      }

      try {
        const data = await apiPostJson(patitasApi('auth'), { email, password });
        if (data && data.ok && data.usuario) {
          const u = data.usuario;
          const usuario = {
            nombre: u.nombre,
            rol: u.rol,
            email: u.email,
            usuarioId: u.usuarioId,
            rolFk: u.rolFk != null ? Number(u.rolFk) : undefined,
          };
          try {
            localStorage.setItem('usuarioActivo', JSON.stringify(usuario));
          } catch (err) {}
          const esAdmin = u.rol === 'admin';
          window.location.href = esAdmin ? authRedirect('panel-admin') : authRedirect('panel');
          return;
        }
        this.mostrarAlerta(alertEl, (data && data.error) || 'Correo o contraseña incorrectos.', 'danger');
      } catch (err) {
        this.mostrarAlerta(alertEl, 'No hay conexión con el servidor. Intenta de nuevo.', 'danger');
      }
    });
  },

  initRegistro() {
    const form = document.getElementById('registro-form');
    const alertEl = document.getElementById('registro-alert');

    if (!form) return;

    this.bindPasswordToggle('registro-password-toggle', 'registro-password');
    this.bindPasswordToggle('registro-password-confirm-toggle', 'registro-password-confirm');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const emailNorm = (document.getElementById('registro-email').value || '').trim().toLowerCase();
      const datos = {
        nombre: document.getElementById('registro-nombre').value?.trim(),
        cedula: document.getElementById('registro-cedula').value?.trim(),
        telefono: document.getElementById('registro-telefono').value?.trim(),
        email: emailNorm,
        password: document.getElementById('registro-password').value,
        passwordConfirm: document.getElementById('registro-password-confirm').value,
      };

      const passMin = Validaciones.longitudMinima(datos.password, 4);
      const passMax = Validaciones.longitudMaxima(datos.password, 200, 'La contraseña');

      const campos = [
        { id: 'registro-nombre', val: Validaciones.nombreCompleto(datos.nombre) },
        { id: 'registro-cedula', val: Validaciones.cedulaCostaRica(datos.cedula) },
        { id: 'registro-telefono', val: Validaciones.telefonoCostaRica(datos.telefono) },
        { id: 'registro-email', val: Validaciones.email(datos.email) },
        {
          id: 'registro-password',
          val:
            passMin.valido && passMax.valido
              ? { valido: true, mensaje: '' }
              : { valido: false, mensaje: passMin.valido ? passMax.mensaje : passMin.mensaje }
        },
        {
          id: 'registro-password-confirm',
          val: Validaciones.contraseñasIguales(datos.password, datos.passwordConfirm)
        }
      ];

      let hayError = false;
      for (const c of campos) {
        if (!c.val.valido) {
          this.mostrarErrorCampo(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          this.limpiarErrorCampo(document.getElementById(c.id));
        }
      }

      if (hayError) return;

      if (typeof apiPostJson !== 'function') {
        this.mostrarAlerta(alertEl, 'No se pudo cargar el cliente API.', 'danger');
        return;
      }

      try {
        const telDigits = (datos.telefono || '').replace(/\D/g, '');
        const telefonoEnvio = telDigits.startsWith('506') && telDigits.length > 8 ? telDigits.slice(3) : telDigits;

        const data = await apiPostJson(patitasApi('registro'), {
          nombre: datos.nombre.replace(/\s+/g, ' ').trim(),
          email: datos.email,
          password: datos.password,
          telefono: telefonoEnvio,
          cedula: datos.cedula.replace(/\D/g, ''),
        });
        if (data && data.ok) {
          this.mostrarAlerta(alertEl, data.mensaje || 'Cuenta creada. Redirigiendo al inicio de sesión…', 'success');
          setTimeout(() => {
            window.location.href = authRedirect('login');
          }, 1200);
          return;
        }
        this.mostrarAlerta(alertEl, (data && data.error) || 'No se pudo registrar.', 'danger');
      } catch (err) {
        this.mostrarAlerta(alertEl, 'Error de red. Intenta de nuevo.', 'danger');
      }
    });
  },

  mostrarAlerta(alertEl, mensaje, tipo) {
    if (!alertEl) return;
    alertEl.className = `alert alert-${tipo}`;
    alertEl.textContent = mensaje;
    alertEl.classList.remove('d-none');
  },

  ocultarAlerta(alertEl) {
    if (!alertEl) return;
    alertEl.classList.add('d-none');
    alertEl.textContent = '';
  },

  mostrarErrorCampo(input, mensaje) {
    if (!input) return;
    input.classList.add('is-invalid');
    input.setAttribute('aria-invalid', 'true');
    const errId = `${input.id}-error`;
    const errorEl = document.getElementById(errId);
    if (errorEl) {
      errorEl.textContent = mensaje;
      errorEl.classList.add('d-block');
      input.setAttribute('aria-describedby', errId);
    }
  },

  limpiarErrorCampo(input) {
    if (!input) return;
    input.classList.remove('is-invalid');
    input.removeAttribute('aria-invalid');
    input.removeAttribute('aria-describedby');
    const errorEl = document.getElementById(`${input.id}-error`);
    if (errorEl) {
      errorEl.textContent = '';
      errorEl.classList.remove('d-block');
    }
  },
};
