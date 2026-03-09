/**
 * Veterinaria Patitas - Autenticación
 * Lógica de login y sesión (hardcoded para entrega actual)
 * Preparado para integrar PHP en la siguiente entrega
 */

const Auth = {
  /** Credenciales demo hardcoded */
  CREDENCIALES: {
    'admin@patitas.com': '1234',
    'cliente@patitas.com': '123456'
  },

  initLogin() {
    const form = document.getElementById('login-form');
    const alertEl = document.getElementById('login-alert');
    const emailInput = document.getElementById('login-email');
    const passwordInput = document.getElementById('login-password');

    if (!form) return;

    form.addEventListener('submit', (e) => {
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

      const claveEsperada = this.CREDENCIALES[email];
      if (claveEsperada && claveEsperada === password) {
        const esAdmin = email === 'admin@patitas.com';
        window.location.href = esAdmin ? 'panel-admin.html' : 'panel.html';
      } else {
        this.mostrarAlerta(alertEl, 'Correo o contraseña incorrectos. Intenta de nuevo.', 'danger');
      }
    });
  },

  initRegistro() {
    const form = document.getElementById('registro-form');
    const alertEl = document.getElementById('registro-alert');

    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const datos = {
        nombre: document.getElementById('registro-nombre').value?.trim(),
        cedula: document.getElementById('registro-cedula').value?.trim(),
        telefono: document.getElementById('registro-telefono').value?.trim(),
        email: document.getElementById('registro-email').value?.trim(),
        password: document.getElementById('registro-password').value,
        passwordConfirm: document.getElementById('registro-password-confirm').value
      };

      const campos = [
        { id: 'registro-nombre', val: Validaciones.requerido(datos.nombre, 'Nombre completo'), nombre: 'nombre' },
        { id: 'registro-cedula', val: Validaciones.requerido(datos.cedula, 'Cédula'), nombre: 'cedula' },
        { id: 'registro-telefono', val: Validaciones.requerido(datos.telefono, 'Teléfono'), nombre: 'telefono' },
        { id: 'registro-email', val: Validaciones.email(datos.email), nombre: 'email' },
        { id: 'registro-password', val: Validaciones.longitudMinima(datos.password, 4), nombre: 'password' },
        { id: 'registro-password-confirm', val: Validaciones.contraseñasIguales(datos.password, datos.passwordConfirm), nombre: 'confirmar contraseña' }
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

      this.mostrarAlerta(alertEl, 'Registro exitoso. Redirigiendo al inicio de sesión...', 'success');
      setTimeout(() => {
        window.location.href = 'login.html';
      }, 1500);
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
  }
};
