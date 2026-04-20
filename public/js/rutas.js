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

/**
 * Cabecera unificada (avatar, nombre, rol) en páginas con partial app-header.
 */
function patitasInicialesSesion(nombre, email) {
  var n = (nombre || "").trim();
  if (n) {
    var p = n.split(/\s+/).filter(function (x) {
      return x.length > 0;
    });
    if (p.length >= 2 && p[0].length && p[1].length) {
      return (p[0].charAt(0) + p[1].charAt(0)).toUpperCase();
    }
    return n.substring(0, 2).toUpperCase();
  }
  var e = (email || "").split("@")[0] || "?";
  return e.substring(0, 2).toUpperCase();
}

function patitasInitSessionHeader() {
  var avatar = document.getElementById("patitasAvatarIniciales");
  if (!avatar) return;

  var raw = null;
  try {
    raw = localStorage.getItem("usuarioActivo");
  } catch (e1) {}
  var u = {};
  try {
    u = raw ? JSON.parse(raw) : {};
  } catch (e2) {}

  var nameEl = document.getElementById("txtNombreUsuario");
  var roleEl = document.getElementById("txtRolUsuario");
  var isAdminLayout = !!document.querySelector(".app-layout.admin-layout");

  if (nameEl && u.nombre) nameEl.textContent = u.nombre;
  if (roleEl) {
    var rfLocal = u.rolFk != null ? Number(u.rolFk) : null;
    if (rfLocal === 3 || u.rol === "cliente") {
      roleEl.textContent = "Cliente";
    } else if (isAdminLayout) {
      roleEl.textContent = "Equipo clínico";
    } else if (rfLocal === 1) {
      roleEl.textContent = "Administrador";
    } else if (rfLocal === 2) {
      roleEl.textContent = "Veterinario";
    } else if (u.rol === "admin") {
      roleEl.textContent = "Equipo clínico";
    } else {
      roleEl.textContent = "Cliente";
    }
  }
  avatar.textContent = patitasInicialesSesion(u.nombre, u.email);

  if (typeof apiGetJson !== "function") return;

  apiGetJson(patitasApi("auth"))
    .then(function (d) {
      if (!d || !d.ok || !d.usuario) return;
      var usr = d.usuario;
      if (nameEl) nameEl.textContent = usr.nombre;
      avatar.textContent = patitasInicialesSesion(usr.nombre, usr.email);
      if (roleEl) {
        var rf = usr.rolFk != null ? Number(usr.rolFk) : null;
        if (rf === 3 || usr.rol === "cliente") {
          roleEl.textContent = "Cliente";
        } else if (isAdminLayout) {
          roleEl.textContent = "Equipo clínico";
        } else if (rf === 1) {
          roleEl.textContent = "Administrador";
        } else if (rf === 2) {
          roleEl.textContent = "Veterinario";
        } else if (usr.rol === "admin") {
          roleEl.textContent = "Equipo clínico";
        } else {
          roleEl.textContent = "Cliente";
        }
      }
      try {
        var merged = Object.assign({}, u, usr);
        if (usr.rolFk != null) merged.rolFk = Number(usr.rolFk);
        localStorage.setItem("usuarioActivo", JSON.stringify(merged));
      } catch (e3) {}
    })
    .catch(function () {});
}

document.addEventListener("DOMContentLoaded", function () {
  patitasInitSessionHeader();

  document.querySelectorAll("a.btn-logout").forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault();
      var url = (typeof patitasApi === 'function') ? apiUrl(patitasApi('logout')) : apiUrl('api.php?route=logout');
      fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: '{}',
      }).catch(function () {});
      try {
        localStorage.removeItem('usuarioActivo');
      } catch (err) {}
      window.location.href = pageRoute('login');
    });
  });
});
