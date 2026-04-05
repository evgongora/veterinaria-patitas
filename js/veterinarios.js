/**
 * Veterinaria Patitas - Gestión de veterinarios
 * Listado y formulario con datos hardcoded
 */

const Veterinarios = {
  /** Listado hardcoded */
  LISTA: [
    { id: 1, cedula: '111111111', nombre: 'Dra. Laura Sánchez', especialidad: 'Medicina General', telefono: '8888-5555' },
    { id: 2, cedula: '222222222', nombre: 'Dr. Pedro Ramírez', especialidad: 'Cirugía', telefono: '8888-6666' },
    { id: 3, cedula: '333333333', nombre: 'Dra. Sofía Castro', especialidad: 'Animales Exóticos', telefono: '8888-7777' }
  ],

  initListado() {
    const tbody = document.querySelector('#tabla-veterinarios tbody');
    if (!tbody) return;

    tbody.innerHTML = this.LISTA.map(v =>
      `<tr>
        <td>${v.cedula}</td>
        <td>${v.nombre}</td>
        <td>${v.especialidad}</td>
        <td>${v.telefono}</td>
        <td class="text-end">
          <a href="${pageRoute('veterinario-formulario', { id: v.id })}" class="btn btn-warning btn-sm">Editar</a>
        </td>
      </tr>`
    ).join('');
  },

  initFormulario() {
    const form = document.getElementById('veterinario-form');
    const alertEl = document.getElementById('veterinario-form-alert');
    if (!form) return;

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      this.ocultarAlerta(alertEl);

      const nombre = document.getElementById('veterinario-nombre').value?.trim();
      const cedula = document.getElementById('veterinario-cedula').value?.trim();
      const especialidad = document.getElementById('veterinario-especialidad').value;
      const telefono = document.getElementById('veterinario-telefono').value?.trim();
      const email = document.getElementById('veterinario-email').value?.trim();

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
          this.mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          this.limpiarError(document.getElementById(c.id));
        }
      });

      if (hayError) return;

      this.mostrarAlerta(alertEl, 'Veterinario guardado exitosamente.', 'success');
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
