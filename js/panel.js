/**
 * Veterinaria Patitas - Panel de control
 * Cards hardcoded y datos de resumen
 */

const Panel = {
  /** Datos hardcoded para el dashboard */
  DATOS: {
    clientes: 12,
    veterinarios: 5,
    citasPendientes: 8,
    mascotas: 28
  },

  init() {
    this.renderCards();
  },

  renderCards() {
    document.getElementById('card-clientes').textContent = this.DATOS.clientes;
    document.getElementById('card-veterinarios').textContent = this.DATOS.veterinarios;
    document.getElementById('card-citas').textContent = this.DATOS.citasPendientes;
    document.getElementById('card-mascotas').textContent = this.DATOS.mascotas;
  }
};

document.addEventListener('DOMContentLoaded', () => Panel.init());
