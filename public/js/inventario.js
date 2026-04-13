/**
 * Inventario — api/inventario.php (administrador y veterinario)
 */
(function () {
  let cacheItems = [];

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente", rolFk: null };
    try {
      const u = JSON.parse(raw);
      return {
        nombre: u.nombre || "Invitado",
        rol: (u.rol || "cliente").toLowerCase(),
        rolFk: u.rolFk != null ? Number(u.rolFk) : null,
      };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente", rolFk: null };
    }
  }

  /** Administrador (1) o veterinario (2), o sesión antigua con rol UI "admin". */
  function esStaff(usuario) {
    if (!usuario) return false;
    const fk = usuario.rolFk;
    if (fk === 1 || fk === 2) return true;
    return usuario.rol === "admin";
  }

  function estadoBadge(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "normal")
      return `<span class="badge" style="background:#DCFCE7;color:#166534;border-radius:999px;">Stock Normal</span>`;
    if (e === "bajo")
      return `<span class="badge" style="background:#FEF3C7;color:#92400E;border-radius:999px;">Stock Bajo</span>`;
    if (e === "critico")
      return `<span class="badge" style="background:#FEE2E2;color:#B91C1C;border-radius:999px;">Stock Crítico</span>`;
    return `<span class="badge bg-secondary">${estado}</span>`;
  }

  function formatearFecha(iso) {
    if (!iso) return "—";
    const [y, m, d] = iso.split("-");
    return `${d}/${m}/${y}`;
  }

  function pintarStats(lista) {
    const total = lista.length;
    const normal = lista.filter((i) => (i.estado || "").toLowerCase() === "normal").length;
    const bajo = lista.filter((i) => (i.estado || "").toLowerCase() === "bajo").length;
    const critico = lista.filter((i) => (i.estado || "").toLowerCase() === "critico").length;

    const elT = document.getElementById("statTotal");
    const elN = document.getElementById("statNormal");
    const elB = document.getElementById("statBajo");
    const elC = document.getElementById("statCritico");

    if (elT) elT.textContent = String(total);
    if (elN) elN.textContent = String(normal);
    if (elB) elB.textContent = String(bajo);
    if (elC) elC.textContent = String(critico);

    const banner = document.getElementById("bannerStock");
    if (!banner) return;

    const alerta = bajo + critico;
    if (alerta > 0) {
      banner.innerHTML = `
        <div class="alert alert-warning d-flex align-items-start gap-3" style="border-left:6px solid #F59E0B;">
          <div class="fs-4" aria-hidden="true">⚠️</div>
          <div>
            <div class="fw-semibold">Atención: ${alerta} medicamentos con stock bajo o crítico</div>
            <div class="small">Se recomienda reposición</div>
          </div>
        </div>`;
    } else {
      banner.innerHTML = "";
    }
  }

  function pintarTabla(esStaffUser) {
    const tbody = document.getElementById("tbodyInventario");
    const resumen = document.getElementById("txtResumenInventario");
    if (!tbody) return;

    const lista = cacheItems;
    pintarStats(lista);
    tbody.innerHTML = "";

    if (lista.length === 0) {
      tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No hay datos</td></tr>`;
      if (resumen) resumen.textContent = "Mostrando 0 items";
      return;
    }

    lista.forEach((i) => {
      const tr = document.createElement("tr");
      const acciones = esStaffUser
        ? `<div class="d-flex gap-1 justify-content-end flex-wrap">
             <a class="btn btn-sm btn-outline-primary" href="${pageRoute("inventario-formulario", { id: i.idNum })}">Editar</a>
             <button type="button" class="btn btn-sm btn-outline-danger btn-inv-eliminar" data-idnum="${i.idNum}">Eliminar</button>
           </div>`
        : `<span class="text-muted small">—</span>`;
      tr.innerHTML = `
        <td class="fw-semibold">${i.nombre}</td>
        <td>${i.cantidad}</td>
        <td>${formatearFecha(i.vencimiento)}</td>
        <td class="text-muted">${i.proveedor || ""}</td>
        <td>${estadoBadge(i.estado)}</td>
        <td class="text-end">${acciones}</td>`;
      tbody.appendChild(tr);
    });

    tbody.querySelectorAll(".btn-inv-eliminar").forEach((btn) => {
      btn.addEventListener("click", function () {
        const idNum = Number(btn.getAttribute("data-idnum"));
        if (!idNum || typeof apiDeleteJson !== "function") return;

        const ejecutar = function () {
          apiDeleteJson("api/inventario.php?idNum=" + encodeURIComponent(String(idNum))).then((data) => {
            if (data && data.ok) {
              cargarDesdeApi().then(() => pintarTabla(esStaffUser));
            } else {
              var err = (data && data.error) || "No se pudo eliminar";
              if (typeof patitasAlerta === "function") {
                patitasAlerta({
                  title: "No se pudo eliminar",
                  message: err,
                  variant: "danger",
                  buttonLabel: "Entendido",
                  buttonClass: "btn-danger",
                });
              }
            }
          });
        };

        patitasConfirmar({
          title: "Dar de baja medicamento",
          message:
            "Se marcará como inactivo en el inventario. ¿Deseas continuar?",
          confirmLabel: "Dar de baja",
          cancelLabel: "Cancelar",
          confirmClass: "btn-danger",
        }).then(function (ok) {
          if (ok) ejecutar();
        });
      });
    });

    if (resumen) resumen.textContent = `Mostrando ${lista.length} ítem(s)`;
  }

  function mostrarSiStaff(usuario) {
    const alerta = document.getElementById("alertaSoloStaff");
    const seccion = document.getElementById("seccionInventario");
    const btnAdd = document.getElementById("btnAgregarMedicamento");

    if (!esStaff(usuario)) {
      if (alerta) {
        alerta.innerHTML = `<div class="alert alert-danger">Esta sección es solo para personal de la clínica (administrador o veterinario).</div>`;
      }
      if (seccion) seccion.classList.add("d-none");
      if (btnAdd) btnAdd.classList.add("d-none");
      return false;
    }

    if (alerta) alerta.innerHTML = "";
    if (seccion) seccion.classList.remove("d-none");
    if (btnAdd) btnAdd.classList.remove("d-none");
    return true;
  }

  function cargarDesdeApi() {
    if (typeof apiGetJson !== "function") return Promise.resolve();
    return apiGetJson("api/inventario.php").then((data) => {
      if (data && data.ok && Array.isArray(data.items)) {
        cacheItems = data.items;
      } else {
        cacheItems = [];
      }
    });
  }

  function initInventario() {
    const tbody = document.getElementById("tbodyInventario");
    if (!tbody) return;

    const usuario = leerUsuarioActivo();
    const ok = mostrarSiStaff(usuario);
    if (!ok) return;

    cargarDesdeApi()
      .then(() => pintarTabla(true))
      .catch(() => {
        tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error al cargar inventario</td></tr>`;
      });
  }

  function llenarSelectTipos(selectEl, tipos, valorSeleccionado) {
    if (!selectEl || !Array.isArray(tipos)) return;
    selectEl.innerHTML = tipos
      .map((t) => `<option value="${t.id}">${t.nombre}</option>`)
      .join("");
    if (valorSeleccionado != null && valorSeleccionado > 0) {
      selectEl.value = String(valorSeleccionado);
    } else if (selectEl.options.length) {
      selectEl.selectedIndex = 0;
    }
  }

  function initFormularioInventario() {
    const form = document.getElementById("formInventario");
    if (!form) return;

    const usuario = leerUsuarioActivo();
    const seccion = document.getElementById("seccionFormularioInventario");
    const titulo = document.getElementById("tituloInventarioForm");
    const bread = document.getElementById("breadcrumbInvAccion");
    const hidId = document.getElementById("idNum");
    const selTipo = document.getElementById("tipoId");

    if (!esStaff(usuario)) {
      if (seccion) seccion.classList.add("d-none");
      const alerta = document.getElementById("alertaInventarioForm");
      if (alerta) {
        alerta.innerHTML = `<div class="alert alert-danger">Solo personal de la clínica puede gestionar medicamentos.</div>`;
      }
      return;
    }

    const params = new URLSearchParams(window.location.search);
    const editId = params.get("id");
    const esEdicion = editId && /^\d+$/.test(editId);

    if (esEdicion) {
      if (titulo) titulo.textContent = "Editar medicamento";
      if (bread) bread.textContent = "Editar medicamento";
      if (hidId) hidId.value = editId;
    }

    function cargarTiposYItem() {
      const urlTipos = esEdicion
        ? "api/inventario.php?id=" + encodeURIComponent(editId)
        : "api/inventario.php?tipos=1";
      return apiGetJson(urlTipos).then((data) => {
        if (!data || !data.ok) return;
        if (Array.isArray(data.tipos)) llenarSelectTipos(selTipo, data.tipos, esEdicion && data.item ? data.item.tipoId : null);
        if (esEdicion && data.item) {
          const n = document.getElementById("nombre");
          const c = document.getElementById("cantidad");
          if (n) n.value = data.item.nombre || "";
          if (c) c.value = data.item.cantidad != null ? String(data.item.cantidad) : "0";
          if (hidId) hidId.value = String(data.item.idNum || editId);
        }
      });
    }

    if (typeof apiGetJson === "function") {
      cargarTiposYItem().catch(() => {});
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const cont = document.getElementById("alertaInventarioForm");
      const nombre = (document.getElementById("nombre") && document.getElementById("nombre").value.trim()) || "";
      const cantidad = Number((document.getElementById("cantidad") && document.getElementById("cantidad").value) || "0");
      const tipoId = Number((selTipo && selTipo.value) || "0");
      const idNum = hidId && hidId.value ? Number(hidId.value) : 0;

      if (!nombre || tipoId <= 0) {
        if (cont) cont.innerHTML = `<div class="alert alert-warning mb-0">Completa nombre y tipo de medicamento.</div>`;
        return;
      }
      if (cantidad < 0) {
        if (cont) cont.innerHTML = `<div class="alert alert-warning mb-0">La cantidad no puede ser negativa.</div>`;
        return;
      }

      const body = { nombre, cantidad, tipoId };
      const prom =
        esEdicion && idNum > 0
          ? apiPutJson("api/inventario.php", Object.assign({ idNum }, body))
          : apiPostJson("api/inventario.php", body);

      prom
        .then(function (data) {
          if (data && data.ok) {
            if (cont) cont.innerHTML = `<div class="alert alert-success mb-0">Medicamento guardado.</div>`;
            setTimeout(function () {
              window.location.href = pageRoute("inventario");
            }, 600);
            return;
          }
          if (cont) cont.innerHTML = `<div class="alert alert-danger mb-0">${(data && data.error) || "No se pudo guardar"}</div>`;
        })
        .catch(function () {
          if (cont) cont.innerHTML = `<div class="alert alert-danger mb-0">Error de red</div>`;
        });
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initInventario();
    initFormularioInventario();
  });
})();
