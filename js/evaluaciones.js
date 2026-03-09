/**
 * Veterinaria Patitas - Evaluaciones / Historial clínico
 */
// evaluaciones para patitas
// se guarda en localStorage para simular envio

(function () {
  const STORAGE_KEY = "patitas_evaluaciones";

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente" };

    try {
      const u = JSON.parse(raw);
      return { nombre: u.nombre || "Invitado", rol: (u.rol || "cliente").toLowerCase() };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente" };
    }
  }

  function ponerUsuarioEnNavbar(usuario) {
    const nombre = document.getElementById("txtNombreUsuario");
    const rol = document.getElementById("txtRolUsuario");
    if (nombre) nombre.textContent = usuario.nombre;
    if (rol) rol.textContent = usuario.rol === "admin" ? "Admin" : "Cliente";
  }

  function leerEvaluaciones() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function guardarEvaluaciones(lista) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));
  }

  function mostrarAlerta(tipo, msg) {
    const cont = document.getElementById("alertaEvaluacion");
    if (!cont) return;

    const clase = tipo === "ok" ? "alert-success" : "alert-danger";
    cont.innerHTML = `<div class="alert ${clase}">${msg}</div>`;
  }

  function obtenerRating() {
    const radios = document.querySelectorAll("input[name='rating']");
    for (const r of radios) {
      if (r.checked) return Number(r.value);
    }
    return 0;
  }

  function init() {
    const form = document.getElementById("formEvaluacion");
    if (!form) return;

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      const rating = obtenerRating();
      const comentario = document.getElementById("comentario").value.trim();

      if (!rating) {
        mostrarAlerta("bad", "Selecciona una puntuacion");
        return;
      }

      if (comentario.length < 3) {
        mostrarAlerta("bad", "Escribe un comentario corto");
        return;
      }

      const lista = leerEvaluaciones();

      lista.unshift({
        id: "EV-" + String(Date.now()).slice(-6),
        usuario: usuario.nombre,
        rol: usuario.rol,
        rating,
        comentario,
        fecha: new Date().toISOString().slice(0, 10),
      });

      guardarEvaluaciones(lista);

      mostrarAlerta("ok", "Listo evaluacion enviada gracias");

      form.reset();

      setTimeout(() => {
        window.location.href = "panel.html";
      }, 900);
    });
  }

  document.addEventListener("DOMContentLoaded", init);
})();