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

  var esStaffLista = !!document.querySelector(".app-layout.admin-layout");

  citasAlertaOcultar("alertCitasLista");

  apiGetJson(patitasApi("citas"))
    .then(function (data) {
      if (!data || !data.ok) {
        var msg =
          (data && data.error) ||
          (esStaffLista
            ? "No se pudieron cargar las citas."
            : "No se pudieron cargar las citas. Inicia sesión como cliente.");
        citasAlertaMostrar("alertCitasLista", msg, "warning");
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-muted">Sin datos</td></tr>';
        return;
      }
      var citas = data.citas || [];
      var esStaff = esStaffLista;
      if (citas.length === 0) {
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-muted">No hay citas registradas</td></tr>';
        return;
      }
      tbody.innerHTML = citas
        .map(function (c) {
          var celdaAccion = esStaff
            ? '<td class="text-end text-muted small">#' + (c.id != null ? c.id : "—") + "</td>"
            : '<td class="text-end text-nowrap"><a href="' +
              pageRoute("cita-formulario", { id: c.id }) +
              '" class="btn btn-sm btn-outline-success me-1">Editar</a><a href="' +
              pageRoute("cita-formulario") +
              '" class="btn btn-sm btn-primary">Nueva</a></td>';
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
            celdaAccion +
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
  if (!selectAnimal || typeof apiGetJson !== "function") return Promise.resolve();

  return apiGetJson(patitasApi("animales")).then(function (data) {
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
  if (!sel || typeof apiGetJson !== "function") return Promise.resolve();

  return apiGetJson(patitasApi("veterinarios")).then(function (data) {
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
  if (!sel || typeof apiGetJson !== "function") return Promise.resolve();

  return apiGetJson(patitasApi("tipos-cita")).then(function (data) {
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

  var hidCita = document.getElementById("citaId");
  var editId = hidCita && hidCita.value ? parseInt(hidCita.value, 10) : 0;
  var req =
    editId > 0
      ? apiPutJson(patitasApi("citas"), Object.assign({ citaId: editId }, body))
      : apiPostJson(patitasApi("citas"), body);

  req
    .then(function (data) {
      if (data && data.ok) {
        if (editId > 0) {
          window.location.href = pageRoute("citas");
          return;
        }
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
        (data && data.error) || (editId > 0 ? "No se pudo guardar la cita." : "No se pudo agendar la cita."),
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

function aplicarHoraSeleccionada(valor) {
  var v = String(valor || "");
  document.querySelectorAll(".horario-btn").forEach(function (b) {
    var inp = b.querySelector('input[name="hora"]');
    if (inp && inp.disabled) {
      b.classList.remove("active", "btn-primary");
      return;
    }
    b.classList.remove("active", "btn-primary");
    if (inp) {
      inp.checked = inp.value === v;
      if (inp.checked) {
        b.classList.add("active", "btn-primary");
      }
    }
  });
}

function exceptCitaIdParaOcupadas() {
  var hid = document.getElementById("citaId");
  if (hid && hid.value) {
    var n = parseInt(hid.value, 10);
    if (n > 0) return n;
  }
  var params = new URLSearchParams(window.location.search);
  return parseInt(params.get("id") || "0", 10) || 0;
}

function bloquearTodosLosHorarios(mensaje) {
  document.querySelectorAll(".horario-btn").forEach(function (b) {
    var inp = b.querySelector('input[name="hora"]');
    if (!inp) return;
    inp.disabled = true;
    inp.checked = false;
    b.classList.add("disabled", "text-muted");
    b.classList.remove("active", "btn-primary");
    b.style.opacity = "0.6";
    b.title = mensaje;
  });
}

function refrescarHorariosOcupados() {
  var fecha = document.getElementById("fecha");
  var vet = document.getElementById("veterinario");
  if (!fecha || !vet || typeof apiGetJson !== "function") return Promise.resolve();
  var fe = fecha.value;
  var vid = parseInt(vet.value, 10);

  if (!fe) {
    bloquearTodosLosHorarios("Elige primero la fecha");
    return Promise.resolve();
  }
  if (vid <= 0) {
    bloquearTodosLosHorarios("Elige veterinario: cada profesional tiene su propia agenda");
    return Promise.resolve();
  }

  var ex = exceptCitaIdParaOcupadas();
  var q = { ocupadas: 1, fecha: fe, veterinarioId: vid };
  if (ex > 0) q.exceptCitaId = ex;
  return apiGetJson(patitasApi("citas", q))
    .then(function (data) {
      var ocup = (data && data.ok && data.horasOcupadas) || [];
      document.querySelectorAll(".horario-btn").forEach(function (b) {
        var inp = b.querySelector('input[name="hora"]');
        if (!inp) return;
        var h = inp.value;
        var bloqueada = ocup.indexOf(h) !== -1;
        inp.disabled = bloqueada;
        if (bloqueada) {
          b.classList.add("disabled", "text-muted");
          b.classList.remove("active", "btn-primary");
          b.style.opacity = "0.55";
          b.title = "Ocupado con el veterinario seleccionado (otro doctor puede tener este hueco libre)";
          inp.checked = false;
        } else {
          b.classList.remove("disabled", "text-muted");
          b.style.opacity = "";
          b.removeAttribute("title");
          inp.disabled = false;
        }
      });
    })
    .catch(function () {
      bloquearTodosLosHorarios("No se pudo consultar la agenda de este veterinario. Intenta de nuevo.");
    });
}

function precargarCitaEdicion() {
  var params = new URLSearchParams(window.location.search);
  var editId = parseInt(params.get("id") || "0", 10);
  if (editId <= 0 || typeof apiGetJson !== "function") return Promise.resolve(null);

  return apiGetJson(patitasApi("citas", { citaId: editId })).then(function (data) {
    if (!data || !data.ok || !data.cita) {
      citasAlertaMostrar("alertCita", (data && data.error) || "No se pudo cargar la cita.", "danger");
      return null;
    }
    var ci = data.cita;
    var animal = document.getElementById("animal");
    var veterinario = document.getElementById("veterinario");
    var tipoCita = document.getElementById("tipoCita");
    var fecha = document.getElementById("fecha");
    var motivo = document.getElementById("motivo");
    if (animal) animal.value = String(ci.animalId || "");
    if (veterinario) veterinario.value = String(ci.veterinarioId || "");
    if (tipoCita) tipoCita.value = String(ci.tipoCitaId || "1");
    if (fecha) fecha.value = ci.fecha || "";
    if (motivo) motivo.value = ci.notas || "";
    aplicarHoraSeleccionada(ci.horaInicio || "");
    if (ci.estadoId !== 3) {
      citasAlertaMostrar(
        "alertCita",
        "Esta cita ya no está pendiente; los cambios están desactivados. Para otros ajustes, contacta a la clínica.",
        "warning"
      );
      var btn = document.getElementById("btnSubmitCita");
      if (btn) btn.disabled = true;
    }
    return ci;
  });
}

document.addEventListener("DOMContentLoaded", function () {
  if (document.getElementById("tablaCitas")) {
    cargarCitas();
  }

  if (document.getElementById("animal")) {
    Promise.all([llenarSelectAnimales(), llenarSelectVeterinarios(), llenarTiposCita()])
      .then(function () {
        return precargarCitaEdicion();
      })
      .then(function (ci) {
        return refrescarHorariosOcupados().then(function () {
          if (ci && ci.horaInicio) aplicarHoraSeleccionada(ci.horaInicio);
        });
      });
    var fe = document.getElementById("fecha");
    var ve = document.getElementById("veterinario");
    if (fe) fe.addEventListener("change", function () { refrescarHorariosOcupados(); });
    if (ve) ve.addEventListener("change", function () { refrescarHorariosOcupados(); });
  }

  if (document.getElementById("resumenCita")) {
    mostrarResumenCita();
  }

  var formularioCita = document.getElementById("formCita");
  if (formularioCita) {
    formularioCita.addEventListener("submit", guardarCita);
  }

  document.querySelectorAll(".horario-btn").forEach(function (btn) {
    btn.addEventListener("click", function (ev) {
      var inp0 = btn.querySelector('input[name="hora"]');
      if (inp0 && inp0.disabled) {
        ev.preventDefault();
        return;
      }
      citasAlertaOcultar("alertCita");
      document.querySelectorAll(".horario-btn").forEach(function (b) {
        var i = b.querySelector('input[name="hora"]');
        if (i && i.disabled) return;
        b.classList.remove("active", "btn-primary");
      });
      btn.classList.add("active", "btn-primary");
      var inp = btn.querySelector('input[name="hora"]');
      if (inp) inp.checked = true;
    });
  });
});
