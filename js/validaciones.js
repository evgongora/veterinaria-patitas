/**
 * Veterinaria Patitas - Validaciones
 * Funciones reutilizables para validación de formularios
 */

const Validaciones = {
  /**
   * Valida que un campo no esté vacío
   * @param {string} valor
   * @param {string} nombreCampo
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
   * Retorna true si el valor está vacío
   * @param {string} valor
   * @returns {boolean}
   */
  campoVacio(valor) {
    return valor === null || valor === undefined || valor.toString().trim() === '';
  },

  /**
   * Valida varios campos requeridos de un formulario
   * @param {Array} campos
   * @returns {boolean}
   */
  validarCamposRequeridos(campos) {
    for (let i = 0; i < campos.length; i++) {
      if (this.campoVacio(campos[i].value)) {
        return false;
      }
    }
    return true;
  },

  /**
   * Valida formato de email
   * @param {string} valor
   * @returns {{ valido: boolean, mensaje: string }}
   */
  email(valor) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const limpio = (valor || '').toString().trim();
    const requerido = this.requerido(limpio, 'El correo electrónico');

    if (!requerido.valido) {
      return requerido;
    }

    return {
      valido: regex.test(limpio),
      mensaje: regex.test(limpio)
        ? ''
        : 'El correo electrónico no tiene un formato válido.'
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
   * @param {number} min
   * @returns {{ valido: boolean, mensaje: string }}
   */
  longitudMinima(valor, min = 4) {
    const v = (valor || '').toString();

    return {
      valido: v.length >= min,
      mensaje: v.length < min
        ? `La contraseña debe tener al menos ${min} caracteres.`
        : ''
    };
  },

  /**
   * Valida que la hora de inicio sea menor que la hora final
   * @param {string} horaInicio
   * @param {string} horaFin
   * @returns {boolean}
   */
  validarHoras(horaInicio, horaFin) {
    return horaInicio < horaFin;
  }
};
