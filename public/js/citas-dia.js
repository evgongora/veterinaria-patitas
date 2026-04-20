/**
 * Citas del día (staff) — route=citas
 */
(function () {
  function fechaLocalYmd() {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
  }

  function badgeEstado(estado) {
    const e = (estado || "").toLowerCase();
    if (e.includes("cancel")) return "bg-danger";
    if (e.includes("complet") || e.includes("confirm")) return "bg-success";
    return "bg-secondary";
  }

  function pintar(lista, hoy) {
    const tbody = document.getElementById("tbodyCitasHoy");
    const sub = document.getElementById("subtituloCitasDia");
    const fechaTxt = document.getElementById("txtFechaHoy");
    if (fechaTxt) {
      try {
        const [yy, mm, dd] = hoy.split("-");
        fechaTxt.textContent = `${dd}/${mm}/${yy}`;
      } catch (e) {
        fechaTxt.textContent = hoy;
      }
    }
    if (!tbody) return;
    if (!lista.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center text-muted py-5">No hay citas programadas para hoy.</td></tr>';
      if (sub) sub.textContent = "Sin citas en la agenda de hoy.";
      return;
    }
    if (sub) sub.textContent = `${lista.length} cita(s) programadas para hoy.`;
    tbody.innerHTML = lista
      .map(
        (c) =>
          `<tr>
          <td class="fw-semibold">${c.horaInicio || "—"}</td>
          <td>${c.animal || "—"}</td>
          <td>${c.veterinario || "—"}</td>
          <td>${c.tipo || "—"}</td>
          <td><span class="badge ${badgeEstado(c.estado)}">${c.estado || "—"}</span></td>
          <td class="text-muted small">${c.id != null ? "#" + c.id : "—"}</td>
        </tr>`
      )
      .join("");
  }

  document.addEventListener("DOMContentLoaded", () => {
    const tbody = document.getElementById("tbodyCitasHoy");
    if (!tbody || typeof apiGetJson !== "function") return;

    const hoy = fechaLocalYmd();
    tbody.innerHTML =
      '<tr><td colspan="6" class="text-center py-4"><span class="spinner-border spinner-border-sm text-success me-2"></span>Cargando citas…</td></tr>';

    apiGetJson(patitasApi("citas", { soloHoy: 1 }))
      .then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML =
            '<tr><td colspan="6" class="text-danger">No se pudieron cargar las citas. ¿Iniciaste sesión como staff?</td></tr>';
          return;
        }
        const citas = data.citas || [];
        const hoyRef = (data.fechaReferencia || hoy).slice(0, 10);
        pintar(citas, hoyRef);
      })
      .catch(() => {
        tbody.innerHTML =
          '<tr><td colspan="6" class="text-danger">Error de red o sesión.</td></tr>';
      });
  });
})();
