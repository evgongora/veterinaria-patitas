/**
 * Veterinaria Patitas - Validaciones
 * Funciones reutilizables para validación de formularios
 */

const Validaciones = {
  requerido(valor, nombreCampo = 'Este campo') {
    const limpio = (valor || '').toString().trim();
    return {
      valido: limpio.length > 0,
      mensaje: limpio.length === 0 ? `${nombreCampo} es requerido.` : ''
    };
  },

  email(valor) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const limpio = (valor || '').toString().trim();
    const vacio = this.requerido(limpio, 'El correo electrónico');
    if (!vacio.valido) return vacio;
    return {
      valido: regex.test(limpio),
      mensaje: regex.test(limpio) ? '' : 'El correo electrónico no tiene un formato válido.'
    };
  },

  contraseñasIguales(password, confirmacion) {
    const p = (password || '').toString();
    const c = (confirmacion || '').toString();
    return {
      valido: p === c && p.length > 0,
      mensaje: p !== c ? 'Las contraseñas no coinciden.' : ''
    };
  },

  longitudMinima(valor, min = 4) {
    const v = (valor || '').toString();
    return {
      valido: v.length >= min,
      mensaje: v.length < min ? `La contraseña debe tener al menos ${min} caracteres.` : ''
    };
  }
};

/** Compatibilidad con mascotas.js */
function validarCamposRequeridos(campos) {
  for (const input of campos) {
    if (!input || (input.value || '').toString().trim() === '') return false;
  }
  return true;
}
