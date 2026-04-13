/**
 * Veterinaria Patitas - Gestión de clientes (api/clientes.php)
 */

const Clientes = {
  initListado() {
    const tbody = document.querySelector('#tabla-clientes tbody');
    if (!tbody || typeof apiGetJson !== 'function') return;

    apiGetJson('api/clientes.php')
      .then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Sin permiso o error al cargar</td></tr>';
          return;
        }
        const list = data.clientes || [];
        if (list.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-muted">No hay clientes</td></tr>';
          return;
        }
        tbody.innerHTML = list.map((c) =>
          `<tr>
            <td>${c.cedula}</td>
            <td>${c.nombre}</td>
            <td>${c.telefono || '—'}</td>
            <td>${c.email || '—'}</td>
            <td class="text-end">
              <a href="${pageRoute('cliente-formulario', { id: c.id })}" class="btn btn-warning btn-sm">Editar</a>
            </td>
          </tr>`
        ).join('');
      })
      .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Error de conexión (¿iniciaste sesión como admin?)</td></tr>';
      });
  },

  initFormulario() {
    const form = document.getElementById('cliente-form');
    const alertEl = document.getElementById('cliente-form-alert');
    const titulo = document.getElementById('tituloClienteForm');
    const bread = document.getElementById('breadcrumbClienteAccion');
    const pwdRow = document.getElementById('cliente-password-row');
    const pwdHint = document.getElementById('cliente-password-hint');
    if (!form) return;

    const params = new URLSearchParams(window.location.search);
    const editId = params.get('id');

    if (editId && typeof apiGetJson === 'function') {
      if (titulo) titulo.textContent = 'Editar cliente';
      if (bread) bread.textContent = 'Editar cliente';
      if (pwdHint) pwdHint.textContent = 'Dejar vacío para no cambiar la contraseña.';
      apiGetJson('api/clientes.php?id=' + encodeURIComponent(editId)).then((data) => {
        if (!data || !data.ok || !data.cliente) {
          if (alertEl) {
            Clientes.mostrarAlerta(alertEl, (data && data.error) || 'No se pudo cargar el cliente.', 'danger');
          }
          return;
        }
        const c = data.cliente;
        const n = document.getElementById('cliente-nombre');
        const ce = document.getElementById('cliente-cedula');
        const t = document.getElementById('cliente-telefono');
        const e = document.getElementById('cliente-email');
        if (n) n.value = c.nombre || '';
        if (ce) ce.value = c.cedula || '';
        if (t) t.value = c.telefono || '';
        if (e) e.value = c.email || '';
      }).catch(() => {
        if (alertEl) Clientes.mostrarAlerta(alertEl, 'Error de red al cargar datos.', 'danger');
      });
    } else {
      if (pwdHint) pwdHint.textContent = 'Mínimo 4 caracteres. El cliente la usará para iniciar sesión.';
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      Clientes.ocultarAlerta(alertEl);

      const nombre = document.getElementById('cliente-nombre').value?.trim();
      const cedula = document.getElementById('cliente-cedula').value?.trim();
      const telefono = document.getElementById('cliente-telefono').value?.trim();
      const email = document.getElementById('cliente-email').value?.trim();
      const password = document.getElementById('cliente-password')?.value || '';

      const campos = [
        { id: 'cliente-nombre', val: Validaciones.requerido(nombre, 'Nombre completo') },
        { id: 'cliente-cedula', val: Validaciones.requerido(cedula, 'Cédula') },
        { id: 'cliente-telefono', val: Validaciones.requerido(telefono, 'Teléfono') },
        { id: 'cliente-email', val: Validaciones.email(email) }
      ];

      let hayError = false;
      campos.forEach(c => {
        if (!c.val.valido) {
          Clientes.mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          Clientes.limpiarError(document.getElementById(c.id));
        }
      });

      if (!editId && password.length < 4) {
        const pe = document.getElementById('cliente-password');
        if (pe) {
          Clientes.mostrarError(pe, 'La contraseña debe tener al menos 4 caracteres');
          hayError = true;
        }
      }

      if (hayError) return;

      if (typeof apiPostJson !== 'function' || typeof apiPutJson !== 'function') {
        Clientes.mostrarAlerta(alertEl, 'Falta api.js', 'danger');
        return;
      }

      const body = { nombre, cedula, telefono, email };
      if (password !== '') body.password = password;

      try {
        let data;
        if (editId) {
          body.clienteId = parseInt(editId, 10);
          data = await apiPutJson('api/clientes.php', body);
        } else {
          body.password = password;
          data = await apiPostJson('api/clientes.php', body);
        }
        if (data && data.ok) {
          Clientes.mostrarAlerta(alertEl, editId ? 'Cliente actualizado correctamente.' : 'Cliente creado correctamente.', 'success');
          setTimeout(() => {
            window.location.href = pageRoute('clientes');
          }, 900);
        } else {
          Clientes.mostrarAlerta(alertEl, (data && data.error) || 'No se pudo guardar.', 'danger');
        }
      } catch (err) {
        Clientes.mostrarAlerta(alertEl, 'Error de red.', 'danger');
      }
    });
  },

  mostrarAlerta(el, mensaje, tipo) {
    if (!el) return;
    el.className = `alert alert-${tipo}`;
    el.textContent = mensaje;
    el.classList.remove('d-none');
  },

  ocultarAlerta(el) {
    if (!el) return;
    el.classList.add('d-none');
    el.textContent = '';
  },

  mostrarError(input, mensaje) {
    if (!input) return;
    input.classList.add('is-invalid');
    const err = document.getElementById(`${input.id}-error`);
    if (err) err.textContent = mensaje;
  },

  limpiarError(input) {
    if (!input) return;
    input.classList.remove('is-invalid');
    const err = document.getElementById(`${input.id}-error`);
    if (err) err.textContent = '';
  }
};
