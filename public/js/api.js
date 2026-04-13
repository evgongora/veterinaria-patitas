/**
 * Cliente AJAX — base URL relativa al sitio (misma carpeta que index.php)
 */
(function (global) {
  function apiUrl(path) {
    const base = String(path || '').replace(/^\//, '');
    try {
      return new URL(base, window.location.href).href;
    } catch (e) {
      return base;
    }
  }

  /**
   * POST JSON
   * @param {string} path ej. 'api/auth.php'
   * @param {object} body
   * @returns {Promise<any>}
   */
  function apiPostJson(path, body) {
    return fetch(apiUrl(path), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body || {}),
    }).then(function (r) {
      return r.json().catch(function () {
        return { ok: false, error: 'Respuesta no JSON' };
      });
    });
  }

  /**
   * GET JSON
   * @param {string} path
   */
  function apiGetJson(path) {
    return fetch(apiUrl(path), {
      method: 'GET',
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    }).then(function (r) {
      return r.json().catch(function () {
        return { ok: false, error: 'Respuesta no JSON' };
      });
    });
  }

  /**
   * PUT JSON
   * @param {string} path
   * @param {object} body
   */
  function apiPutJson(path, body) {
    return fetch(apiUrl(path), {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(body || {}),
    }).then(function (r) {
      return r.json().catch(function () {
        return { ok: false, error: 'Respuesta no JSON' };
      });
    });
  }

  /**
   * DELETE (sin cuerpo; usar query params en path)
   */
  function apiDeleteJson(path) {
    return fetch(apiUrl(path), {
      method: 'DELETE',
      headers: { Accept: 'application/json' },
      credentials: 'same-origin',
    }).then(function (r) {
      return r.json().catch(function () {
        return { ok: false, error: 'Respuesta no JSON' };
      });
    });
  }

  global.apiUrl = apiUrl;
  global.apiPostJson = apiPostJson;
  global.apiGetJson = apiGetJson;
  global.apiPutJson = apiPutJson;
  global.apiDeleteJson = apiDeleteJson;
})(typeof window !== 'undefined' ? window : this);
