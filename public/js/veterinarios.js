/**
 * Veterinaria Patitas - Veterinarios (api/veterinarios.php)
 */

function patitasVeterinariosEsAdmin() {
  try {
    const u = JSON.parse(localStorage.getItem('usuarioActivo') || '{}');
    return Number(u.rolFk) === 1;
  } catch (e) {
    return false;
  }
}

const Veterinarios = {
  initListado() {
    const tbody = document.querySelector('#tabla-veterinarios tbody');
    const btnNuevo = document.getElementById('btnNuevoVeterinario');
    if (!tbody || typeof apiGetJson !== 'function') return;

    const pintar = (esAdmin) => {
      if (btnNuevo) btnNuevo.classList.toggle('d-none', !esAdmin);
      apiGetJson('api/veterinarios.php')
        .then((data) => {
          if (!data || !data.ok) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Error al cargar</td></tr>';
            return;
          }
          const list = data.veterinarios || [];
          if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted">No hay veterinarios</td></tr>';
            return;
          }
          tbody.innerHTML = list.map((v) => {
            const acciones = esAdmin
              ? `<a href="${pageRoute('veterinario-formulario', { id: v.id })}" class="btn btn-warning btn-sm">Editar</a>`
              : '<span class="text-muted small">—</span>';
            return `<tr>
            <td>${v.id}</td>
            <td>${v.nombreCompleto}</td>
            <td>${v.especialidad || '—'}</td>
            <td>${v.telefono || '—'}</td>
            <td class="text-end">${acciones}</td>
          </tr>`;
          }).join('');
        })
        .catch(() => {
          tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Error de conexión</td></tr>';
        });
    };

    apiGetJson('api/auth.php')
      .then((d) => {
        const esAdmin = d && d.ok && d.usuario && Number(d.usuario.rolFk) === 1;
        try {
          if (d && d.ok && d.usuario) {
            const u = JSON.parse(localStorage.getItem('usuarioActivo') || '{}');
            localStorage.setItem('usuarioActivo', JSON.stringify(Object.assign({}, u, d.usuario)));
          }
        } catch (e2) {}
        pintar(esAdmin);
      })
      .catch(() => pintar(patitasVeterinariosEsAdmin()));
  },

  initFormulario() {
    const form = document.getElementById('veterinario-form');
    const alertEl = document.getElementById('veterinario-form-alert');
    const titulo = document.getElementById('tituloVeterinarioForm');
    const bread = document.getElementById('breadcrumbVetAccion');
    const pwdHint = document.getElementById('veterinario-password-hint');
    if (!form) return;

    const wireForm = () => {
    const params = new URLSearchParams(window.location.search);
    const editId = params.get('id');

    if (editId && typeof apiGetJson === 'function') {
      if (titulo) titulo.textContent = 'Editar veterinario';
      if (bread) bread.textContent = 'Editar veterinario';
      if (pwdHint) pwdHint.textContent = 'Dejar vacío para no cambiar la contraseña.';
      apiGetJson('api/veterinarios.php?id=' + encodeURIComponent(editId)).then((data) => {
        if (!data || !data.ok || !data.veterinario) {
          if (alertEl) {
            Veterinarios.mostrarAlerta(alertEl, (data && data.error) || 'No se pudo cargar el veterinario.', 'danger');
          }
          return;
        }
        const v = data.veterinario;
        const n = document.getElementById('veterinario-nombre');
        const ce = document.getElementById('veterinario-cedula');
        const esp = document.getElementById('veterinario-especialidad');
        const t = document.getElementById('veterinario-telefono');
        const e = document.getElementById('veterinario-email');
        if (n) n.value = v.nombre || '';
        if (ce) ce.value = v.cedula || '';
        if (esp) esp.value = v.especialidad || '';
        if (t) t.value = v.telefono || '';
        if (e) e.value = v.email || '';
      }).catch(() => {
        if (alertEl) Veterinarios.mostrarAlerta(alertEl, 'Error de red al cargar datos.', 'danger');
      });
    } else {
      if (pwdHint) pwdHint.textContent = 'Mínimo 4 caracteres. El veterinario la usará para iniciar sesión.';
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      Veterinarios.ocultarAlerta(alertEl);

      const nombre = document.getElementById('veterinario-nombre').value?.trim();
      const cedula = document.getElementById('veterinario-cedula').value?.trim();
      const especialidad = document.getElementById('veterinario-especialidad').value;
      const telefono = document.getElementById('veterinario-telefono').value?.trim();
      const email = document.getElementById('veterinario-email').value?.trim();
      const password = document.getElementById('veterinario-password')?.value || '';

      const campos = [
        { id: 'veterinario-nombre', val: Validaciones.requerido(nombre, 'Nombre completo') },
        { id: 'veterinario-cedula', val: Validaciones.requerido(cedula, 'Cédula') },
        { id: 'veterinario-especialidad', val: Validaciones.requerido(especialidad, 'Especialidad') },
        { id: 'veterinario-telefono', val: Validaciones.requerido(telefono, 'Teléfono') },
        { id: 'veterinario-email', val: Validaciones.email(email) }
      ];

      let hayError = false;
      campos.forEach(c => {
        if (!c.val.valido) {
          Veterinarios.mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          Veterinarios.limpiarError(document.getElementById(c.id));
        }
      });

      if (!editId && password.length < 4) {
        const pe = document.getElementById('veterinario-password');
        if (pe) {
          Veterinarios.mostrarError(pe, 'La contraseña debe tener al menos 4 caracteres');
          hayError = true;
        }
      }

      if (hayError) return;

      if (typeof apiPostJson !== 'function' || typeof apiPutJson !== 'function') {
        Veterinarios.mostrarAlerta(alertEl, 'Falta api.js', 'danger');
        return;
      }

      const body = { nombre, cedula, especialidad, telefono, email };
      if (password !== '') body.password = password;

      try {
        let data;
        if (editId) {
          body.veterinarioId = parseInt(editId, 10);
          data = await apiPutJson('api/veterinarios.php', body);
        } else {
          body.password = password;
          data = await apiPostJson('api/veterinarios.php', body);
        }
        if (data && data.ok) {
          Veterinarios.mostrarAlerta(alertEl, editId ? 'Veterinario actualizado correctamente.' : 'Veterinario creado correctamente.', 'success');
          setTimeout(() => {
            window.location.href = pageRoute('veterinarios');
          }, 900);
        } else {
          Veterinarios.mostrarAlerta(alertEl, (data && data.error) || 'No se pudo guardar.', 'danger');
        }
      } catch (err) {
        Veterinarios.mostrarAlerta(alertEl, 'Error de red.', 'danger');
      }
    });
    };

    if (typeof apiGetJson !== 'function') {
      if (!patitasVeterinariosEsAdmin()) {
        Veterinarios.mostrarAlerta(alertEl, 'Solo el administrador puede crear o editar cuentas de veterinarios.', 'warning');
        form.querySelectorAll('input, select, button[type="submit"]').forEach((el) => {
          el.disabled = true;
        });
      } else {
        wireForm();
      }
      return;
    }

    apiGetJson('api/auth.php')
      .then((d) => {
        if (d && d.ok && d.usuario) {
          try {
            const u = JSON.parse(localStorage.getItem('usuarioActivo') || '{}');
            localStorage.setItem('usuarioActivo', JSON.stringify(Object.assign({}, u, d.usuario)));
          } catch (e) {}
        }
        const esAdmin = d && d.ok && d.usuario && Number(d.usuario.rolFk) === 1;
        if (!esAdmin) {
          Veterinarios.mostrarAlerta(alertEl, 'Solo el administrador puede crear o editar cuentas de veterinarios.', 'warning');
          form.querySelectorAll('input, select, button[type="submit"]').forEach((el) => {
            el.disabled = true;
          });
          return;
        }
        wireForm();
      })
      .catch(() => {
        if (!patitasVeterinariosEsAdmin()) {
          Veterinarios.mostrarAlerta(alertEl, 'Solo el administrador puede crear o editar cuentas de veterinarios.', 'warning');
          form.querySelectorAll('input, select, button[type="submit"]').forEach((el) => {
            el.disabled = true;
          });
        } else {
          wireForm();
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
