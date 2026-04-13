/**
 * Reportes — métricas desde api/dashboard.php (vista staff)
 */
(function () {
  function formatearColones(n) {
    return "₡" + Number(n || 0).toLocaleString("es-CR");
  }

  document.addEventListener("DOMContentLoaded", () => {
    const alerta = document.getElementById("reportes-alerta");
    const grid = document.getElementById("reportes-grid");
    if (!grid || typeof apiGetJson !== "function") return;

    apiGetJson("api/dashboard.php")
      .then((data) => {
        if (!data || !data.ok) {
          if (alerta)
            alerta.innerHTML =
              '<div class="alert alert-warning">No se pudieron cargar los datos. Inicia sesión como administrador.</div>';
          return;
        }
        if (data.vista !== "staff") {
          if (alerta)
            alerta.innerHTML =
              '<div class="alert alert-danger">Esta sección es solo para personal autorizado.</div>';
          grid.classList.add("d-none");
          return;
        }
        const s = data.stats || {};
        const set = (id, val) => {
          const el = document.getElementById(id);
          if (el) el.textContent = val != null ? String(val) : "—";
        };
        set("repClientes", s.usuarios);
        set("repAnimales", s.animales);
        set("repCitasHoy", s.citasHoy);
        set("repCitasPend", s.citasPendientes);
        set("repInventario", s.inventarioItems);
        set("repVets", s.veterinarios);
        const ing = document.getElementById("repIngresos");
        if (ing) ing.textContent = formatearColones(s.ingresosMes);
        if (alerta) alerta.innerHTML = "";
      })
      .catch(() => {
        if (alerta)
          alerta.innerHTML =
            '<div class="alert alert-danger">Error de conexión.</div>';
      });
  });
})();
