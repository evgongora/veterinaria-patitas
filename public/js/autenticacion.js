/**
 * Veterinaria Patitas - Autenticación
 * Login: POST api/auth.php | Registro: POST api/registro.php
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
  initLogin() {
    const form = document.getElementById('login-form');
    const alertEl = document.getElementById('login-alert');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const email = (emailInput.value || '').trim().toLowerCase();
      const password = (passwordInput.value || '').trim();

      const emailVal = Validaciones.email(email);
      const passwordVal = Validaciones.requerido(password, 'La contraseña');

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
      } else {
        this.limpiarErrorCampo(passwordInput);
      }

      if (hayError) return;

      if (typeof apiPostJson !== 'function') {
        this.mostrarAlerta(alertEl, 'No se pudo cargar el cliente API.', 'danger');
        return;
      }

      try {
        const data = await apiPostJson('api/auth.php', { email, password });
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

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const datos = {
        nombre: document.getElementById('registro-nombre').value?.trim(),
        cedula: document.getElementById('registro-cedula').value?.trim(),
        telefono: document.getElementById('registro-telefono').value?.trim(),
        email: document.getElementById('registro-email').value?.trim(),
        password: document.getElementById('registro-password').value,
        passwordConfirm: document.getElementById('registro-password-confirm').value,
      };

      const campos = [
        { id: 'registro-nombre', val: Validaciones.requerido(datos.nombre, 'Nombre completo') },
        { id: 'registro-cedula', val: Validaciones.requerido(datos.cedula, 'Cédula') },
        { id: 'registro-telefono', val: Validaciones.requerido(datos.telefono, 'Teléfono') },
        { id: 'registro-email', val: Validaciones.email(datos.email) },
        { id: 'registro-password', val: Validaciones.longitudMinima(datos.password, 4) },
        {
          id: 'registro-password-confirm',
          val: Validaciones.contraseñasIguales(datos.password, datos.passwordConfirm),
        },
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
        const data = await apiPostJson('api/registro.php', {
          nombre: datos.nombre,
          email: datos.email,
          password: datos.password,
          telefono: datos.telefono,
          cedula: datos.cedula,
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
    const errorEl = document.getElementById(`${input.id}-error`);
    if (errorEl) errorEl.textContent = mensaje;
  },

  limpiarErrorCampo(input) {
    if (!input) return;
    input.classList.remove('is-invalid');
    const errorEl = document.getElementById(`${input.id}-error`);
    if (errorEl) errorEl.textContent = '';
  },
};
