/**
 * Veterinaria Patitas - Gestión de clientes
 * Listado y formulario con datos hardcoded
 */

const Clientes = {
  /** Listado hardcoded */
  LISTA: [
    { id: 1, cedula: '101110111', nombre: 'María González', telefono: '8888-1111', email: 'maria@email.com' },
    { id: 2, cedula: '202220222', nombre: 'Juan Pérez', telefono: '8888-2222', email: 'juan@email.com' },
    { id: 3, cedula: '303330333', nombre: 'Ana Rodríguez', telefono: '8888-3333', email: 'ana@email.com' },
    { id: 4, cedula: '404440444', nombre: 'Carlos Mora', telefono: '8888-4444', email: 'carlos@email.com' }
  ],

  initListado() {
    const tbody = document.querySelector('#tabla-clientes tbody');
    if (!tbody) return;

    tbody.innerHTML = this.LISTA.map(c =>
      `<tr>
        <td>${c.cedula}</td>
        <td>${c.nombre}</td>
        <td>${c.telefono}</td>
        <td>${c.email}</td>
        <td class="text-end">
          <a href="cliente-formulario.html?id=${c.id}" class="btn btn-warning btn-sm">Editar</a>
        </td>
      </tr>`
    ).join('');
  },

  initFormulario() {
    const form = document.getElementById('cliente-form');
    const alertEl = document.getElementById('cliente-form-alert');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const nombre = document.getElementById('cliente-nombre').value?.trim();
      const cedula = document.getElementById('cliente-cedula').value?.trim();
      const telefono = document.getElementById('cliente-telefono').value?.trim();
      const email = document.getElementById('cliente-email').value?.trim();

      const campos = [
        { id: 'cliente-nombre', val: Validaciones.requerido(nombre, 'Nombre completo') },
        { id: 'cliente-cedula', val: Validaciones.requerido(cedula, 'Cédula') },
        { id: 'cliente-telefono', val: Validaciones.requerido(telefono, 'Teléfono') },
        { id: 'cliente-email', val: Validaciones.email(email) }
      ];

      let hayError = false;
      campos.forEach(c => {
        if (!c.val.valido) {
          this.mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          this.limpiarError(document.getElementById(c.id));
        }
      });

      if (hayError) return;

      this.mostrarAlerta(alertEl, 'Cliente guardado exitosamente.', 'success');
      form.reset();
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
