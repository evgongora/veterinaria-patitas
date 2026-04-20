/**
 * Historial clínico — route=historial&animalId=
 */
(function () {
  function escapeHtml(s) {
    return String(s || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function cargarAnimalesSelect() {
    const sel = document.getElementById("selAnimal");
    const tbody = document.getElementById("tbodyHistorial");
    if (!sel || typeof apiGetJson !== "function") return;

    apiGetJson(patitasApi("animales")).then((data) => {
      if (!data || !data.ok) {
        sel.innerHTML = '<option value="">No se pudieron cargar las mascotas</option>';
        if (tbody) {
          tbody.innerHTML =
            '<tr><td colspan="4" class="text-danger">Inicia sesión o vuelve a intentar.</td></tr>';
        }
        return;
      }
      const list = data.animales || [];
      if (list.length === 0) {
        sel.innerHTML = '<option value="">Sin mascotas registradas</option>';
        if (tbody) {
          tbody.innerHTML =
            '<tr><td colspan="4" class="text-muted">Registra una mascota para ver citas aquí.</td></tr>';
        }
        return;
      }
      sel.innerHTML = list
        .map(
          (a) =>
            `<option value="${a.id}">${a.nombre} — ${a.especie}</option>`
        )
        .join("");
      const params = new URLSearchParams(window.location.search);
      const pre = params.get("animal");
      if (pre) sel.value = pre;
      sel.addEventListener("change", () => cargarHistorial(sel.value));
      cargarHistorial(sel.value);
    })
      .catch(() => {
        sel.innerHTML = '<option value="">Error de red</option>';
        if (tbody) {
          tbody.innerHTML =
            '<tr><td colspan="4" class="text-danger">Comprueba tu conexión e intenta de nuevo.</td></tr>';
        }
      });
  }

  function cargarHistorial(animalId) {
    const tbody = document.getElementById("tbodyHistorial");
    if (!tbody || !animalId || typeof apiGetJson !== "function") return;

    apiGetJson(patitasApi("historial", { animalId: animalId }))
      .then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Error al cargar</td></tr>`;
          return;
        }
        const rows = data.registros || [];
        if (rows.length === 0) {
          tbody.innerHTML = `<tr><td colspan="4" class="text-muted">Sin citas registradas para esta mascota</td></tr>`;
          return;
        }
        tbody.innerHTML = rows
          .map(
            (r) =>
              `<tr>
            <td>${escapeHtml(r.fecha)}</td>
            <td>${escapeHtml(r.diagnostico)}</td>
            <td>${escapeHtml(r.tratamiento)}</td>
            <td>${escapeHtml(r.veterinario)}</td>
          </tr>`
          )
          .join("");
      })
      .catch(() => {
        tbody.innerHTML = `<tr><td colspan="4" class="text-danger">Sin sesión o error de red</td></tr>`;
      });
  }

  document.addEventListener("DOMContentLoaded", () => {
    cargarAnimalesSelect();
  });
})();
