/**
 * Rutas del front controller PHP (index.php?r=...)
 * Usar en redirecciones y enlaces generados desde JavaScript.
 *
 * @param {string} route Nombre de la ruta (views/pages/{route}.php)
 * @param {Record<string, string|number>|undefined} extraParams Parámetros GET adicionales
 * @returns {string}
 */
function pageRoute(route, extraParams) {
  const p = new URLSearchParams();
  p.set('r', route);
  if (extraParams && typeof extraParams === 'object') {
    Object.keys(extraParams).forEach((k) => {
      if (extraParams[k] !== undefined && extraParams[k] !== null) {
        p.set(k, String(extraParams[k]));
      }
    });
  }
  const rel = 'index.php?' + p.toString();
  try {
    if (typeof window !== 'undefined' && window.location && window.location.href) {
      return new URL(rel, window.location.href).href;
    }
  } catch (e) {
    /* fallback */
  }
  return rel;
}
