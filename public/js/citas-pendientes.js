/**
 * Citas pendientes de aprobación (staff) — api.php?route=citas&pendientes=1
 */
(function () {
  function alerta(msg, tipo) {
    var el = document.getElementById("alertPendientes");
    if (!el) return;
    el.className = "alert alert-" + (tipo || "info") + " mb-3";
    el.textContent = msg;
    el.classList.remove("d-none");
  }

  function ocultarAlerta() {
    var el = document.getElementById("alertPendientes");
    if (!el) return;
    el.classList.add("d-none");
    el.textContent = "";
  }

  function cargar() {
    var tbody = document.getElementById("tbodyPendientes");
    var sub = document.getElementById("subtituloPendientes");
    if (!tbody || typeof apiGetJson !== "function") return;

    ocultarAlerta();
    tbody.innerHTML =
      '<tr><td colspan="7" class="text-center py-4"><span class="spinner-border spinner-border-sm text-success me-2"></span>Cargando…</td></tr>';

    apiGetJson(patitasApi("citas", { pendientes: 1 }))
      .then(function (data) {
        if (!data || !data.ok) {
          alerta((data && data.error) || "No se pudo cargar la lista.", "danger");
          tbody.innerHTML =
            '<tr><td colspan="7" class="text-center text-muted py-4">Sin datos</td></tr>';
          return;
        }
        var lista = data.citas || [];
        if (sub) {
          sub.textContent =
            lista.length === 0
              ? "No hay citas pendientes de confirmar."
              : lista.length + " cita(s) esperando tu confirmación.";
        }
        if (lista.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="7" class="text-center text-muted py-5">Todo al día. No hay solicitudes pendientes.</td></tr>';
          return;
        }
        tbody.innerHTML = lista
          .map(function (c) {
            return (
              "<tr>" +
              "<td>" +
              (c.fecha || "") +
              "</td>" +
              "<td class=\"fw-semibold\">" +
              (c.horaInicio || "") +
              "</td>" +
              "<td>" +
              (c.cliente || "") +
              "</td>" +
              "<td>" +
              (c.animal || "") +
              "</td>" +
              "<td>" +
              (c.veterinario || "") +
              "</td>" +
              "<td>" +
              (c.tipo || "") +
              "</td>" +
              "<td class=\"text-end\">" +
              '<button type="button" class="btn btn-sm btn-success btn-aceptar-cita" data-id="' +
              c.id +
              '">Aceptar</button>' +
              "</td>" +
              "</tr>"
            );
          })
          .join("");

        tbody.querySelectorAll(".btn-aceptar-cita").forEach(function (btn) {
          btn.addEventListener("click", function () {
            var id = parseInt(btn.getAttribute("data-id") || "0", 10);
            if (id <= 0) return;
            btn.disabled = true;
            ocultarAlerta();
            apiPostJson(patitasApi("citas"), { accion: "aceptar", citaId: id })
              .then(function (res) {
                if (res && res.ok) {
                  alerta("Cita aceptada.", "success");
                  cargar();
                  return;
                }
                alerta((res && res.error) || "No se pudo aceptar.", "warning");
                btn.disabled = false;
              })
              .catch(function () {
                alerta("Error de red.", "danger");
                btn.disabled = false;
              });
          });
        });
      })
      .catch(function () {
        alerta("Error de red o sesión.", "danger");
        tbody.innerHTML =
          '<tr><td colspan="7" class="text-center text-danger py-4">No se pudo cargar</td></tr>';
      });
  }

  document.addEventListener("DOMContentLoaded", cargar);
})();
