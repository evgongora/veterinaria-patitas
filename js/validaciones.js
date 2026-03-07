/**
 * Veterinaria Patitas - Validaciones
 * Funciones reutilizables para validación de formularios
 */

const Validaciones = {
  /**
   * Valida que un campo no esté vacío
   * @param {string} valor
   * @param {string} nombreCampo - Nombre para mensajes de error
   * @returns {{ valido: boolean, mensaje: string }}
   */
  requerido(valor, nombreCampo = 'Este campo') {
    const limpio = (valor || '').toString().trim();
    return {
      valido: limpio.length > 0,
      mensaje: limpio.length === 0 ? `${nombreCampo} es requerido.` : ''
    };
  },

  /**
   * Valida formato de email
   * @param {string} valor
   * @returns {{ valido: boolean, mensaje: string }}
   */
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

  /**
   * Valida que contraseña y confirmación coincidan
   * @param {string} password
   * @param {string} confirmacion
   * @returns {{ valido: boolean, mensaje: string }}
   */
  contraseñasIguales(password, confirmacion) {
    const p = (password || '').toString();
    const c = (confirmacion || '').toString();
    return {
      valido: p === c && p.length > 0,
      mensaje: p !== c ? 'Las contraseñas no coinciden.' : ''
    };
  },

  /**
   * Valida longitud mínima de contraseña
   * @param {string} valor
   * @param {number} min - Longitud mínima (default 4)
   * @returns {{ valido: boolean, mensaje: string }}
   */
  longitudMinima(valor, min = 4) {
    const v = (valor || '').toString();
    return {
      valido: v.length >= min,
      mensaje: v.length < min ? `La contraseña debe tener al menos ${min} caracteres.` : ''
    };
  }
};
