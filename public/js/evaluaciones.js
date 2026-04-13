/**
 * Evaluaciones — GET según rol (staff / cliente / anon) + POST/PUT/DELETE clientes.
 */
(function () {
  function escapeHtml(s) {
    return String(s || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function mostrarAlerta(tipo, msg) {
    const cont = document.getElementById("alertaEvaluacion");
    if (!cont) return;
    const clase = tipo === "ok" ? "success" : "danger";
    cont.innerHTML = `<div class="alert alert-${clase}">${escapeHtml(msg)}</div>`;
  }

  function obtenerRating() {
    const radios = document.querySelectorAll("input[name='rating']");
    for (const r of radios) {
      if (r.checked) return Number(r.value);
    }
    return 0;
  }

  function setRatingValue(n) {
    const v = Math.min(5, Math.max(1, Number(n) || 0));
    if (!v) return;
    const el = document.getElementById("star" + v);
    if (el) el.checked = true;
  }

  function estrellasHtml(n) {
    const x = Math.min(5, Math.max(0, n));
    return "★".repeat(x) + "☆".repeat(5 - x);
  }

  function formatearFecha(iso) {
    if (!iso) return "";
    try {
      const d = new Date(String(iso).replace(" ", "T"));
      if (Number.isNaN(d.getTime())) return iso;
      return d.toLocaleDateString("es-CR", {
        day: "numeric",
        month: "short",
        year: "numeric",
      });
    } catch (e) {
      return iso;
    }
  }

  function showPanel(vista) {
    /** @type {Record<string, string>} */
    const map = { staff: "panelEvalStaff", cliente: "panelEvalCliente", anon: "panelEvalAnon" };
    ["panelEvalStaff", "panelEvalCliente", "panelEvalAnon"].forEach(function (id) {
      const el = document.getElementById(id);
      if (el) el.classList.add("d-none");
    });
    const target = document.getElementById(map[vista]);
    if (target) target.classList.remove("d-none");
  }

  function renderListaPublica(wrap, evaluaciones) {
    if (!wrap) return;
    if (!evaluaciones || evaluaciones.length === 0) {
      wrap.innerHTML = '<p class="text-muted small mb-0">Aún no hay opiniones publicadas.</p>';
      return;
    }
    wrap.innerHTML = evaluaciones
      .map(function (ev) {
        const autor = ev.autor || "Usuario";
        const com = escapeHtml(ev.comentario || "");
        return (
          '<div class="border rounded-3 p-3 mb-2 bg-light">' +
          '<div class="d-flex justify-content-between align-items-start gap-2 mb-1">' +
          '<span class="fw-semibold small">' +
          escapeHtml(autor) +
          "</span>" +
          '<span class="text-warning small" aria-label="' +
          ev.rating +
          ' de 5">' +
          estrellasHtml(ev.rating) +
          "</span>" +
          "</div>" +
          '<p class="mb-1 small">' +
          com +
          "</p>" +
          '<div class="text-muted" style="font-size:0.75rem;">' +
          formatearFecha(ev.fecha_creado) +
          "</div>" +
          "</div>"
        );
      })
      .join("");
  }

  function renderStaffTabla(rows) {
    const tbody = document.getElementById("tbodyEvalStaff");
    if (!tbody) return;
    if (!rows || rows.length === 0) {
      tbody.innerHTML = '<tr><td colspan="5" class="text-muted">No hay evaluaciones.</td></tr>';
      return;
    }
    tbody.innerHTML = rows
      .map(function (r) {
        return (
          "<tr>" +
          "<td class=\"small text-nowrap\">" +
          escapeHtml(formatearFecha(r.fecha_creado)) +
          "</td>" +
          "<td><span class=\"fw-semibold small\">" +
          escapeHtml(r.nombreCompleto || "-") +
          "</span></td>" +
          "<td class=\"small\">" +
          escapeHtml(r.email || "") +
          "</td>" +
          '<td class="text-center"><span class="badge bg-warning text-dark">' +
          r.rating +
          "/5</span></td>" +
          "<td><span class=\"small\">" +
          escapeHtml(r.comentario || "") +
          "</span></td>" +
          "</tr>"
        );
      })
      .join("");
  }

  function renderMisEvaluaciones(mias) {
    const wrap = document.getElementById("listaMisEvaluaciones");
    if (!wrap) return;
    if (!mias || mias.length === 0) {
      wrap.innerHTML = '<p class="text-muted small mb-0">Aún no has enviado evaluaciones.</p>';
      return;
    }
    wrap.innerHTML = mias
      .map(function (ev) {
        const com = escapeHtml(ev.comentario || "");
        return (
          '<div class="border rounded-3 p-3 mb-2 bg-white d-flex flex-wrap justify-content-between align-items-start gap-2" data-id="' +
          ev.id +
          '">' +
          '<div class="flex-grow-1">' +
          '<div class="d-flex align-items-center gap-2 mb-1">' +
          '<span class="badge bg-success">' +
          ev.rating +
          "/5</span>" +
          '<span class="text-muted small">' +
          formatearFecha(ev.fecha_creado) +
          "</span>" +
          "</div>" +
          '<p class="mb-0 small">' +
          com +
          "</p>" +
          "</div>" +
          '<div class="d-flex gap-1">' +
          '<button type="button" class="btn btn-sm btn-outline-primary btn-editar-eval" data-id="' +
          ev.id +
          '" data-rating="' +
          ev.rating +
          '" data-comentario="' +
          encodeURIComponent(ev.comentario || "") +
          '">Editar</button>' +
          '<button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-eval" data-id="' +
          ev.id +
          '">Eliminar</button>' +
          "</div>" +
          "</div>"
        );
      })
      .join("");

    wrap.querySelectorAll(".btn-editar-eval").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const id = Number(btn.getAttribute("data-id"));
        const rating = Number(btn.getAttribute("data-rating"));
        let comentario = "";
        try {
          comentario = decodeURIComponent(btn.getAttribute("data-comentario") || "");
        } catch (e1) {
          comentario = "";
        }
        document.getElementById("evalId").value = String(id);
        setRatingValue(rating);
        const ta = document.getElementById("comentario");
        if (ta) ta.value = comentario;
        const titulo = document.getElementById("tituloFormEval");
        if (titulo) titulo.textContent = "Editar evaluación";
        const bc = document.getElementById("btnCancelarEdicionEval");
        if (bc) bc.classList.remove("d-none");
        const bs = document.getElementById("btnSubmitEval");
        if (bs) bs.textContent = "Guardar cambios";
        window.scrollTo({ top: document.getElementById("formEvaluacion").offsetTop - 80, behavior: "smooth" });
      });
    });

    wrap.querySelectorAll(".btn-eliminar-eval").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const id = Number(btn.getAttribute("data-id"));
        if (!id || typeof apiDeleteJson !== "function") return;

        const ejecutarBorrado = function () {
          apiDeleteJson("api/evaluaciones.php?id=" + encodeURIComponent(String(id))).then(function (data) {
            if (data && data.ok) {
              mostrarAlerta("ok", "Evaluación eliminada.");
              cargarDatos();
            } else {
              mostrarAlerta("bad", (data && data.error) || "No se pudo eliminar");
            }
          });
        };

        patitasConfirmar({
          title: "Eliminar evaluación",
          message: "Esta opinión se quitará de tu historial y de la lista pública. ¿Continuar?",
          confirmLabel: "Eliminar",
          cancelLabel: "Cancelar",
          confirmClass: "btn-danger",
        }).then(function (ok) {
          if (ok) ejecutarBorrado();
        });
      });
    });
  }

  function resetFormularioNuevo() {
    const form = document.getElementById("formEvaluacion");
    if (form) form.reset();
    const hid = document.getElementById("evalId");
    if (hid) hid.value = "";
    const titulo = document.getElementById("tituloFormEval");
    if (titulo) titulo.textContent = "Nueva evaluación";
    const bc = document.getElementById("btnCancelarEdicionEval");
    if (bc) bc.classList.add("d-none");
    const bs = document.getElementById("btnSubmitEval");
    if (bs) bs.textContent = "Enviar evaluación";
  }

  function cargarDatos() {
    if (typeof apiGetJson !== "function") return;
    apiGetJson("api/evaluaciones.php").then(function (data) {
      if (!data || !data.ok) {
        mostrarAlerta("bad", (data && data.error) || "No se pudieron cargar las evaluaciones.");
        return;
      }

      if (data.vista === "staff") {
        showPanel("staff");
        renderStaffTabla(data.evaluaciones || []);
        return;
      }

      if (data.vista === "cliente") {
        showPanel("cliente");
        renderListaPublica(document.getElementById("listaEvaluacionesPub"), data.publicas || []);
        renderMisEvaluaciones(data.mias || []);
        return;
      }

      if (data.vista === "anon") {
        showPanel("anon");
        renderListaPublica(document.getElementById("listaEvaluacionesAnon"), data.publicas || []);
      }
    });
  }

  function init() {
    const form = document.getElementById("formEvaluacion");
    const btnCancel = document.getElementById("btnCancelarEdicionEval");
    if (btnCancel) {
      btnCancel.addEventListener("click", function () {
        resetFormularioNuevo();
      });
    }

    if (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        const rol = parseInt(document.body.getAttribute("data-rol-fk") || "0", 10);
        if (rol !== 3) {
          mostrarAlerta("bad", "Solo los clientes pueden enviar evaluaciones.");
          return;
        }

        if (typeof apiPostJson !== "function" || typeof apiPutJson !== "function") {
          mostrarAlerta("bad", "Falta api.js");
          return;
        }

        const rating = obtenerRating();
        const comentario = document.getElementById("comentario").value.trim();
        const evalId = (document.getElementById("evalId") && document.getElementById("evalId").value.trim()) || "";

        if (!rating) {
          mostrarAlerta("bad", "Selecciona una puntuación");
          return;
        }
        if (comentario.length < 3) {
          mostrarAlerta("bad", "Escribe un comentario");
          return;
        }

        const payload = { rating: rating, comentario: comentario };
        const prom =
          evalId !== ""
            ? apiPutJson("api/evaluaciones.php", Object.assign({ id: parseInt(evalId, 10) }, payload))
            : apiPostJson("api/evaluaciones.php", payload);

        prom.then(function (data) {
          if (data && data.ok) {
            mostrarAlerta("ok", evalId ? "evaluación actualizada." : "Evaluación guardada. Gracias.");
            resetFormularioNuevo();
            cargarDatos();
            return;
          }
          if (data && data.error === "No autenticado") {
            mostrarAlerta("bad", "Tu sesión expiró. Vuelve a iniciar sesión.");
            return;
          }
          if (data && data.error === "Solo clientes") {
            mostrarAlerta("bad", "Solo los clientes pueden enviar evaluaciones.");
            return;
          }
          mostrarAlerta("bad", (data && data.error) || "No se pudo guardar.");
        });
      });
    }

    cargarDatos();
  }

  document.addEventListener("DOMContentLoaded", init);
})();
