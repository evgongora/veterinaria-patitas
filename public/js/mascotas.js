function mascotasAlertaMostrar(mensaje, tipo) {
  var el = document.getElementById("alertMascota");
  if (!el) {
    if (typeof patitasAlerta === "function") {
      var titulos = { danger: "Error", warning: "Atención", success: "Listo", info: "Aviso" };
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

function mascotasAlertaOcultar() {
  var el = document.getElementById("alertMascota");
  if (!el) return;
  el.classList.add("d-none");
  el.textContent = "";
}

function cargarMascotas() {
  var cont = document.getElementById("contenedor-mascotas");
  if (!cont || typeof apiGetJson !== "function") return;

  apiGetJson(patitasApi("animales"))
    .then(function (data) {
      if (!data || !data.ok) {
        cont.innerHTML =
          '<p class="text-muted">No se pudieron cargar los animales.</p>';
        return;
      }
      var list = data.animales || [];
      cont.innerHTML = "";
      if (list.length === 0) {
        cont.innerHTML =
          '<div class="col-12"><p class="text-muted">No hay animales registrados.</p></div>';
        return;
      }
      list.forEach(function (m) {
        var art = document.createElement("article");
        art.className = "col-12 col-lg-4";
        art.innerHTML =
          '<div class="card pet-card h-100">' +
          '<div class="card-body p-4">' +
          '<div class="d-flex justify-content-between align-items-start mb-3">' +
          '<div class="pet-avatar"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>' +
          '<div class="d-flex gap-2">' +
          '<a href="' +
          pageRoute("mascota-formulario", { id: m.id }) +
          '" class="btn btn-sm btn-link text-secondary p-0" title="Editar"><i class="bi bi-pencil-square" aria-hidden="true"></i></a>' +
          "</div></div>" +
          '<h3 class="h5 fw-bold mb-1">' +
          (m.nombre || "") +
          "</h3>" +
          '<p class="text-muted small mb-3">' +
          (m.especie || "") +
          "</p>" +
          '<div class="pet-detail mb-2"><span class="label">Raza:</span><span class="value">' +
          (m.raza || "") +
          "</span></div>" +
          '<div class="pet-detail mb-2"><span class="label">Edad:</span><span class="value">' +
          (m.edad != null ? m.edad + " años" : "—") +
          "</span></div>" +
          '<div class="pet-detail mb-2"><span class="label">Sexo:</span><span class="value">' +
          (m.sexo || "") +
          "</span></div>" +
          '<div class="pet-detail mb-2"><span class="label">Peso:</span><span class="value">' +
          (m.peso != null ? m.peso + " kg" : "—") +
          "</span></div>" +
          '<p class="small mb-3"><strong>Observaciones:</strong> ' +
          (m.observaciones || "—") +
          "</p>" +
          '<a href="' +
          pageRoute("historial-clinico", { animal: m.id }) +
          '" class="btn btn-outline-primary w-100">Ver Historial Clínico</a>' +
          "</div></div>";
        cont.appendChild(art);
      });
    })
    .catch(function () {
      cont.innerHTML =
        '<p class="text-danger">Error al cargar animales. Inicia sesión como cliente.</p>';
    });
}

function cargarRazasSelect() {
  var sel = document.getElementById("razaId");
  if (!sel || typeof apiGetJson !== "function") return;

  apiGetJson(patitasApi("razas")).then(function (data) {
    if (!data || !data.ok) return;
    var razas = data.razas || [];
    sel.innerHTML =
      '<option value="">Seleccione raza</option>' +
      razas
        .map(function (r) {
          return (
            '<option value="' +
            r.id +
            '">' +
            (r.especie || "") +
            " — " +
            (r.raza || "") +
            "</option>"
          );
        })
        .join("");
  });
}

function cargarMascotaFormulario() {
  var params = new URLSearchParams(window.location.search);
  var id = params.get("id");
  if (!id) return;

  if (typeof apiGetJson !== "function") return;

  apiGetJson(patitasApi("animales")).then(function (data) {
    if (!data || !data.ok) return;
    var m = (data.animales || []).find(function (x) {
      return String(x.id) === String(id);
    });
    if (!m) return;
    var n = document.getElementById("nombre");
    var r = document.getElementById("razaId");
    var e = document.getElementById("edad");
    var s = document.getElementById("sexo");
    var p = document.getElementById("peso");
    var o = document.getElementById("observaciones");
    if (n) n.value = m.nombre || "";
    if (r) r.value = String(m.razaId || "");
    if (e) e.value = m.edad != null ? m.edad : "";
    if (s) s.value = m.sexoCodigo === "F" ? "Hembra" : "Macho";
    if (p) p.value = m.peso != null ? m.peso : "";
    if (o) o.value = m.observaciones || "";
  });
}

function guardarMascota(event) {
  event.preventDefault();
  mascotasAlertaOcultar();

  if (typeof apiPostJson !== "function") {
    mascotasAlertaMostrar("Falta cargar el script de API (api.js).", "danger");
    return;
  }

  var nombre = document.getElementById("nombre");
  var razaId = document.getElementById("razaId");
  var edad = document.getElementById("edad");
  var sexo = document.getElementById("sexo");
  var peso = document.getElementById("peso");
  var obs = document.getElementById("observaciones");

  if (!nombre || !nombre.value.trim()) {
    mascotasAlertaMostrar("Indica el nombre del animal.", "warning");
    return;
  }
  if (!razaId || !razaId.value) {
    mascotasAlertaMostrar("Selecciona la raza.", "warning");
    return;
  }

  var sexoVal = "M";
  if (sexo && sexo.value === "Hembra") sexoVal = "F";

  var body = {
    nombre: nombre.value.trim(),
    razaId: parseInt(razaId.value, 10),
    edad: edad && edad.value ? parseInt(edad.value, 10) : 0,
    sexo: sexoVal,
    peso: peso && peso.value ? parseFloat(peso.value) : 0,
    observaciones: obs ? obs.value.trim() : "",
  };

  var params = new URLSearchParams(window.location.search);
  var editId = params.get("id");
  if (editId) body.animalId = parseInt(editId, 10);

  apiPostJson(patitasApi("animales"), body)
    .then(function (data) {
      if (data && data.ok) {
        var msg = editId
          ? "Cambios guardados. Redirigiendo a Mis animales…"
          : "Animal registrado. Redirigiendo a Mis animales…";
        mascotasAlertaMostrar(msg, "success");
        window.setTimeout(function () {
          window.location.href = pageRoute("mascotas");
        }, 1100);
        return;
      }
      mascotasAlertaMostrar((data && data.error) || "No se pudo guardar.", "danger");
    })
    .catch(function () {
      mascotasAlertaMostrar(
        "Error de red. Intenta de nuevo en unos segundos.",
        "danger"
      );
    });
}

document.addEventListener("DOMContentLoaded", function () {
  cargarMascotas();
  cargarRazasSelect();
  cargarMascotaFormulario();

  var formularioMascota = document.getElementById("formMascota");
  if (formularioMascota) {
    formularioMascota.addEventListener("submit", guardarMascota);
  }
});
