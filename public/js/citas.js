function obtenerClaseEstado(estado) {
  var e = (estado || "").toLowerCase();
  if (e.indexOf("complet") !== -1 || e.indexOf("confirm") !== -1) return "badge-exito";
  if (e.indexOf("pendient") !== -1) return "badge-advertencia";
  if (e.indexOf("cancel") !== -1) return "badge-error";
  return "badge-advertencia";
}

function citasAlertaMostrar(idEl, mensaje, tipo) {
  var el = document.getElementById(idEl);
  if (!el) {
    if (typeof patitasAlerta === "function") {
      var titulos = { danger: "Error", warning: "Revisa los datos", success: "Listo", info: "Aviso" };
      var vars = { danger: "danger", warning: "warning", success: "success", info: "info" };
      patitasAlerta({
        title: titulos[tipo] || "Aviso",
        message: mensaje,
        variant: vars[tipo] || "info",
        buttonLabel: "Entendido",
      });
    }
    return;
  }
  el.className = "alert alert-" + tipo + " mb-3";
  el.textContent = mensaje;
  el.classList.remove("d-none");
}

function citasAlertaOcultar(idEl) {
  var el = document.getElementById(idEl);
  if (!el) return;
  el.classList.add("d-none");
  el.textContent = "";
}

function cargarCitas() {
  var tbody = document.getElementById("tablaCitas");
  if (!tbody || typeof apiGetJson !== "function") return;

  citasAlertaOcultar("alertCitasLista");

  apiGetJson("api/citas.php")
    .then(function (data) {
      if (!data || !data.ok) {
        var msg =
          (data && data.error) ||
          "No se pudieron cargar las citas. Inicia sesión como cliente.";
        citasAlertaMostrar("alertCitasLista", msg, "warning");
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-muted">Sin datos</td></tr>';
        return;
      }
      var citas = data.citas || [];
      if (citas.length === 0) {
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-muted">No hay citas registradas</td></tr>';
        return;
      }
      tbody.innerHTML = citas
        .map(function (c) {
          return (
            "<tr>" +
            "<td>" +
            (c.animal || "") +
            "</td>" +
            "<td>" +
            (c.veterinario || "") +
            "</td>" +
            "<td>" +
            (c.fecha || "") +
            "</td>" +
            "<td>" +
            (c.horaInicio || "") +
            " - " +
            (c.horaFin || "") +
            "</td>" +
            "<td>" +
            (c.tipo || "") +
            "</td>" +
            "<td><span class=\"" +
            obtenerClaseEstado(c.estado) +
            "\">" +
            (c.estado || "") +
            "</span></td>" +
            "<td><a href=\"" +
            pageRoute("cita-formulario") +
            "\" class=\"btn btn-sm btn-primary\">Nueva</a></td>" +
            "</tr>"
          );
        })
        .join("");
    })
    .catch(function () {
      citasAlertaMostrar(
        "alertCitasLista",
        "Error de red. Comprueba tu conexión e intenta de nuevo.",
        "danger"
      );
      tbody.innerHTML =
        '<tr><td colspan="7" class="text-danger">Error de conexión</td></tr>';
    });
}

function llenarSelectAnimales() {
  var selectAnimal = document.getElementById("animal");
  if (!selectAnimal || typeof apiGetJson !== "function") return;

  apiGetJson("api/animales.php").then(function (data) {
    if (!data || !data.ok) return;
    var list = data.animales || [];
    var opts = '<option value="">Seleccione un animal</option>';
    list.forEach(function (a) {
      opts +=
        '<option value="' +
        a.id +
        '">' +
        a.nombre +
        " (" +
        (a.especie || "") +
        ")</option>";
    });
    selectAnimal.innerHTML = opts;
  });
}

function llenarSelectVeterinarios() {
  var sel = document.getElementById("veterinario");
  if (!sel || typeof apiGetJson !== "function") return;

  apiGetJson("api/veterinarios.php").then(function (data) {
    if (!data || !data.ok) return;
    var list = data.veterinarios || [];
    var opts = '<option value="">Seleccione un veterinario</option>';
    list.forEach(function (v) {
      opts +=
        '<option value="' +
        v.id +
        '">' +
        (v.nombreCompleto || "") +
        "</option>";
    });
    sel.innerHTML = opts;
  });
}

function llenarTiposCita() {
  var sel = document.getElementById("tipoCita");
  if (!sel || typeof apiGetJson !== "function") return;

  apiGetJson("api/tipos-cita.php").then(function (data) {
    if (!data || !data.ok) return;
    var list = data.tipos || [];
    sel.innerHTML = list
      .map(function (t) {
        return (
          '<option value="' +
          t.id +
          '">' +
          (t.nombre || t.DESCRIPCION || "") +
          "</option>"
        );
      })
      .join("");
  });
}

function guardarCita(event) {
  event.preventDefault();
  citasAlertaOcultar("alertCita");

  if (typeof apiPostJson !== "function") {
    citasAlertaMostrar("alertCita", "Falta cargar el script de API (api.js).", "danger");
    return;
  }

  var animal = document.getElementById("animal");
  var veterinario = document.getElementById("veterinario");
  var fecha = document.getElementById("fecha");
  var tipoCita = document.getElementById("tipoCita");
  var motivo = document.getElementById("motivo");
  var horaRadio = document.querySelector('input[name="hora"]:checked');

  if (!animal || !animal.value) {
    citasAlertaMostrar("alertCita", "Selecciona un animal.", "warning");
    return;
  }
  if (!veterinario || !veterinario.value) {
    citasAlertaMostrar("alertCita", "Selecciona un veterinario.", "warning");
    return;
  }
  if (!fecha || !fecha.value) {
    citasAlertaMostrar("alertCita", "Indica la fecha de la cita.", "warning");
    return;
  }
  if (!horaRadio) {
    citasAlertaMostrar("alertCita", "Selecciona un horario disponible.", "warning");
    return;
  }

  var body = {
    animalId: parseInt(animal.value, 10),
    veterinarioId: parseInt(veterinario.value, 10),
    fecha: fecha.value,
    horaInicio: horaRadio.value,
    tipoCitaId: tipoCita ? parseInt(tipoCita.value, 10) || 1 : 1,
    notas: motivo ? motivo.value.trim() : "",
  };

  apiPostJson("api/citas.php", body)
    .then(function (data) {
      if (data && data.ok) {
        sessionStorage.setItem(
          "citaActual",
          JSON.stringify({
            animalId: body.animalId,
            fecha: body.fecha,
            hora: body.horaInicio,
          })
        );
        window.location.href = pageRoute("cita-confirmacion");
        return;
      }
      citasAlertaMostrar(
        "alertCita",
        (data && data.error) || "No se pudo agendar la cita.",
        "danger"
      );
    })
    .catch(function () {
      citasAlertaMostrar(
        "alertCita",
        "Error de red. Intenta de nuevo en unos segundos.",
        "danger"
      );
    });
}

function mostrarResumenCita() {
  var contenedor = document.getElementById("resumenCita");
  if (!contenedor) return;

  var citaGuardada = sessionStorage.getItem("citaActual");
  if (!citaGuardada) {
    contenedor.innerHTML =
      '<p class="text-muted mb-0">No hay datos de una cita reciente. <a href="' +
      (typeof pageRoute === "function" ? pageRoute("cita-formulario") : "index.php?r=cita-formulario") +
      '">Agendar una cita</a>.</p>';
    return;
  }
  var cita = JSON.parse(citaGuardada);
  var fecha = cita.fecha || "";
  var hora = cita.hora || "";
  contenedor.innerHTML =
    '<div class="alert alert-success mb-0 border-0" role="status">' +
    '<p class="mb-3 fw-semibold">Listo: tu cita ya está en el sistema.</p>' +
    '<dl class="row mb-0 small">' +
    '<dt class="col-sm-3">Fecha</dt><dd class="col-sm-9">' +
    fecha +
    "</dd>" +
    '<dt class="col-sm-3">Hora</dt><dd class="col-sm-9">' +
    hora +
    "</dd>" +
    "</dl></div>";
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("tablaCitas")) {
    cargarCitas();
  }

  if (document.getElementById("animal")) {
    llenarSelectAnimales();
    llenarSelectVeterinarios();
    llenarTiposCita();
  }

  if (document.getElementById("resumenCita")) {
    mostrarResumenCita();
  }

  var formularioCita = document.getElementById("formCita");
  if (formularioCita) {
    formularioCita.addEventListener("submit", guardarCita);
  }

  document.querySelectorAll(".horario-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      citasAlertaOcultar("alertCita");
      document.querySelectorAll(".horario-btn").forEach(function (b) {
        b.classList.remove("active", "btn-primary");
      });
      btn.classList.add("active", "btn-primary");
      var inp = btn.querySelector('input[name="hora"]');
      if (inp) inp.checked = true;
    });
  });
});
