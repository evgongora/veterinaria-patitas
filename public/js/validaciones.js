/**
 * Veterinaria Patitas - Validaciones
 * Funciones reutilizables para validación de formularios
 */

function patitasAdjetivoObligatorio(nombreCampo) {
  const s = (nombreCampo || '').trim().toLowerCase();
  if (s.startsWith('la ')) return 'obligatoria';
  if (s.startsWith('el ')) return 'obligatorio';
  if (/\b(teléfono|telefono)\b/u.test(s)) return 'obligatorio';
  if (/\b(correo|email|nombre)\b/u.test(s)) return 'obligatorio';
  if (/\b(contraseña|cédula|cedula|especialidad)\b/u.test(s)) return 'obligatoria';
  return 'obligatorio';
}

const Validaciones = {
  requerido(valor, nombreCampo = 'Este campo') {
    const limpio = (valor || '').toString().trim();
    if (limpio.length > 0) {
      return { valido: true, mensaje: '' };
    }
    const adj = patitasAdjetivoObligatorio(nombreCampo);
    return {
      valido: false,
      mensaje: `${nombreCampo} es ${adj}.`
    };
  },

  longitudMaxima(valor, max, nombreCampo = 'Este campo') {
    const v = (valor || '').toString();
    return {
      valido: v.length <= max,
      mensaje: v.length > max ? `${nombreCampo} no puede superar ${max} caracteres.` : ''
    };
  },

  email(valor) {
    const regex =
      /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
    const limpio = (valor || '').toString().trim();
    const vacio = this.requerido(limpio, 'El correo electrónico');
    if (!vacio.valido) return vacio;
    const len = this.longitudMaxima(limpio, 254, 'El correo electrónico');
    if (!len.valido) return len;
    return {
      valido: regex.test(limpio),
      mensaje: regex.test(limpio) ? '' : 'El correo electrónico no tiene un formato válido.'
    };
  },

  /**
   * Nombre y al menos un apellido (coincide con cómo se parte en el servidor).
   * Letras con acentos, apóstrofes y guiones; sin solo números o símbolos raros.
   */
  nombreCompleto(valor) {
    const limpio = (valor || '').toString().replace(/\s+/g, ' ').trim();
    const req = this.requerido(limpio, 'El nombre completo');
    if (!req.valido) return req;
    const partes = limpio.split(' ').filter(Boolean);
    if (partes.length < 2) {
      return {
        valido: false,
        mensaje: 'Indica tu nombre y al menos un apellido.'
      };
    }
    const lenTotal = this.longitudMaxima(limpio, 150, 'El nombre completo');
    if (!lenTotal.valido) return lenTotal;
    const palabraOk = /^[\p{L}]+(?:['.-][\p{L}]+)*\.?$/u;
    for (const p of partes) {
      const token = p.length > 1 && p.endsWith('.') ? p.slice(0, -1) : p;
      if (token.length < 2 || !palabraOk.test(p)) {
        return {
          valido: false,
          mensaje: 'Usa solo letras en el nombre (puedes usar apóstrofe o guion, p. ej. O\'Connor). Sin números.'
        };
      }
    }
    for (const p of partes) {
      if (p.length > 50) {
        return { valido: false, mensaje: 'Cada nombre o apellido no puede superar 50 caracteres.' };
      }
    }
    return { valido: true, mensaje: '' };
  },

  /** Cédula física/jurídica CR: solo dígitos, 9 o 10 tras limpiar. */
  cedulaCostaRica(valor) {
    const d = (valor || '').toString().replace(/\D/g, '');
    const req = this.requerido(d, 'La cédula');
    if (!req.valido) return req;
    if (d.length < 9 || d.length > 10) {
      return {
        valido: false,
        mensaje: 'La cédula debe tener 9 o 10 dígitos (sin letras).'
      };
    }
    return { valido: true, mensaje: '' };
  },

  /** Teléfono CR: al menos 8 dígitos (local o con 506). */
  telefonoCostaRica(valor) {
    let d = (valor || '').toString().replace(/\D/g, '');
    if (d.startsWith('506') && d.length > 8) {
      d = d.slice(3);
    }
    const req = this.requerido(d, 'El teléfono');
    if (!req.valido) return req;
    if (d.length < 8 || d.length > 12) {
      return {
        valido: false,
        mensaje: 'Ingresa un teléfono válido (8 dígitos o más, solo números).'
      };
    }
    return { valido: true, mensaje: '' };
  },

  contraseñasIguales(password, confirmacion) {
    const p = (password || '').toString();
    const c = (confirmacion || '').toString();
    if (c.length === 0) {
      return { valido: false, mensaje: 'Confirma la contraseña.' };
    }
    if (p !== c) {
      return { valido: false, mensaje: 'Las contraseñas no coinciden.' };
    }
    return { valido: true, mensaje: '' };
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
