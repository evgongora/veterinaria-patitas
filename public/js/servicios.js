/**
 * Catálogo de servicios: GET público; alta/edición/baja (desactivar) vía api/servicios.php (personal: admin y veterinario).
 */
(function () {
  const STORAGE_KEY = "patitas_servicios";

  function leerServicios() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];

    try {
      const data = JSON.parse(raw);
      if (!Array.isArray(data)) return [];
      return data;
    } catch (e) {
      return [];
    }
  }

  function guardarServicios(lista) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));
  }

  function claseBiServicio(icono) {
    const o = (icono || "").trim();
    if (o.includes("💉")) return "bi bi-droplet-half";
    if (o.includes("🛁")) return "bi bi-droplet";
    if (o.includes("💊")) return "bi bi-capsule-pill";
    if (o.includes("✂️")) return "bi bi-scissors";
    if (o.includes("🩺")) return "bi bi-heart-pulse";
    if (o.includes("🐾")) return "bi bi-heart-fill";
    return "bi bi-heart-pulse";
  }

  async function cargarServiciosDesdeApi() {
    if (typeof apiGetJson !== "function") return;
    try {
      const data = await apiGetJson("api/servicios.php");
      if (data && data.ok && Array.isArray(data.servicios)) {
        guardarServicios(data.servicios);
      }
    } catch (e) {
      /* sin caché */
    }
  }

  function formatearColones(monto) {
    if (!monto) return "Consultar";
    return "₡" + Number(monto).toLocaleString("es-CR");
  }

  function badgeEstado(estado) {
    const esActivo = (estado || "").toLowerCase() === "activo";
    const clase = esActivo ? "bg-success" : "bg-secondary";
    return `<span class="badge ${clase}">${estado}</span>`;
  }

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

  function getIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id");
  }

  function mostrarErrores(idCont, lista) {
    const cont = document.getElementById(idCont);
    if (!cont) return;

    if (!lista.length) {
      cont.innerHTML = "";
      return;
    }

    cont.innerHTML = `
      <div class="alert alert-danger">
        <div class="fw-semibold mb-1">Revisa esto</div>
        <ul class="mb-0">
          ${lista.map((e) => `<li>${e}</li>`).join("")}
        </ul>
      </div>
    `;
  }

  function mostrarExito(idCont, msg) {
    const cont = document.getElementById(idCont);
    if (!cont) return;
    cont.innerHTML = `<div class="alert alert-success">${msg}</div>`;
  }

  function pintarCardsCliente() {
    const cont = document.getElementById("cardsServicios");
    if (!cont) return;

    const servicios = leerServicios().filter((s) => s.estado === "Activo");
    cont.innerHTML = "";

    servicios.forEach((s) => {
      const col = document.createElement("div");
      col.className = "col-12 col-md-6 col-lg-4 col-xl-3";

      const precioStr = s.precio > 0 ? formatearColones(s.precio) : "Consultar";
      const duracion = s.duracionMin ? s.duracionMin + " min" : "";
      const bi = claseBiServicio(s.icono);

      col.innerHTML = `
        <a class="text-decoration-none text-dark servicio-card-link" href="${pageRoute("cita-formulario", { servicio: s.id })}">
          <div class="card shadow-sm border-0 h-100 servicio-card-hover">
            <div class="card-body p-4">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:56px;height:56px;background:rgba(46,125,50,0.15);color:#2E7D32;font-size:1.35rem;"><i class="${bi}" aria-hidden="true"></i></div>
              <h3 class="h6 fw-bold mb-1">${s.nombre}</h3>
              <p class="text-muted small mb-2">${s.descripcion}</p>
              <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold text-success">${precioStr}</span>
                ${duracion ? '<span class="text-muted small">' + duracion + "</span>" : ""}
              </div>
            </div>
          </div>
        </a>
      `;

      cont.appendChild(col);
    });
  }

  function obtenerFiltros() {
    const txt = document.getElementById("txtBuscarServicio")?.value.trim().toLowerCase() || "";
    const estado = document.getElementById("selEstadoServicio")?.value || "Todos";
    return { txt, estado };
  }

  function aplicarFiltros(servicios, filtros) {
    let res = [...servicios];

    if (filtros.txt) {
      res = res.filter((s) => {
        const todo = `${s.nombre} ${s.descripcion}`.toLowerCase();
        return todo.includes(filtros.txt);
      });
    }

    if (filtros.estado !== "Todos") {
      res = res.filter((s) => s.estado === filtros.estado);
    }

    return res;
  }

  function pintarStatsAdmin(servicios) {
    const activos = servicios.filter((s) => s.estado === "Activo").length;
    const inactivos = servicios.filter((s) => s.estado === "Inactivo").length;

    const elA = document.getElementById("statActivos");
    const elI = document.getElementById("statInactivos");

    if (elA) elA.textContent = activos;
    if (elI) elI.textContent = inactivos;
  }

  function pintarTablaAdmin() {
    const tbody = document.getElementById("tbodyServicios");
    const resumen = document.getElementById("txtResumenServicios");
    if (!tbody) return;

    const servicios = leerServicios();
    const filtros = obtenerFiltros();
    const filtrados = aplicarFiltros(servicios, filtros);

    pintarStatsAdmin(servicios);

    tbody.innerHTML = "";

    if (filtrados.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            No hay servicios con esos filtros
          </td>
        </tr>
      `;
      if (resumen) resumen.textContent = "Mostrando 0 servicios";
      return;
    }

    filtrados.forEach((s) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td class="fw-semibold">${s.nombre}</td>
        <td class="text-muted d-none d-md-table-cell">${s.descripcion}</td>
        <td>${formatearColones(s.precio)}</td>
        <td>${s.duracionMin} min</td>
        <td>${badgeEstado(s.estado)}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary"
             href="${pageRoute("servicio-formulario", { id: s.id })}">
            Editar
          </a>
          <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                  data-accion="eliminar"
                  data-id="${s.id}">
            Desactivar
          </button>
        </td>
      `;

      tbody.appendChild(tr);
    });

    if (resumen) {
      resumen.textContent = `Mostrando ${filtrados.length} servicio(s) de ${servicios.length}`;
    }

    tbody.querySelectorAll("button[data-accion='eliminar']").forEach((btn) => {
      btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-id");
        eliminarServicio(id);
      });
    });
  }

  function eliminarServicio(id) {
    const servicios = leerServicios();
    const serv = servicios.find((s) => s.id === id);
    if (!serv) return;

    if (typeof patitasConfirmar !== "function" || typeof apiDeleteJson !== "function") {
      if (typeof patitasAlerta === "function") {
        patitasAlerta({
          title: "Error",
          message: "No se puede desactivar el servicio. Recarga la página.",
          variant: "danger",
        });
      }
      return;
    }

    patitasConfirmar({
      title: "Desactivar servicio",
      message:
        '¿Desactivar "' +
        serv.nombre +
        '"? Dejará de mostrarse en el catálogo público. El registro permanece en la base de datos.',
      confirmLabel: "Desactivar",
      cancelLabel: "Cancelar",
      confirmClass: "btn-danger",
    }).then(function (ok) {
      if (!ok) return;
      apiDeleteJson("api/servicios.php?id=" + encodeURIComponent(id)).then(function (data) {
        if (data && data.ok) {
          void cargarServiciosDesdeApi().then(function () {
            pintarTablaAdmin();
            if (typeof patitasAlerta === "function") {
              patitasAlerta({
                title: "Listo",
                message: "Servicio desactivado correctamente.",
                variant: "success",
              });
            }
          });
        } else {
          const err = (data && data.error) || "No se pudo desactivar.";
          if (typeof patitasAlerta === "function") {
            patitasAlerta({ title: "Error", message: err, variant: "danger" });
          }
        }
      });
    });
  }

  function configurarEventosAdmin() {
    const txtBuscar = document.getElementById("txtBuscarServicio");
    const selEstado = document.getElementById("selEstadoServicio");
    const btnLimpiar = document.getElementById("btnLimpiarFiltros");

    if (txtBuscar) txtBuscar.addEventListener("input", pintarTablaAdmin);
    if (selEstado) selEstado.addEventListener("change", pintarTablaAdmin);

    if (btnLimpiar) {
      btnLimpiar.addEventListener("click", () => {
        if (txtBuscar) txtBuscar.value = "";
        if (selEstado) selEstado.value = "Todos";
        pintarTablaAdmin();
      });
    }
  }

  function initVistaServiciosPorRol(usuario) {
    const cards = document.getElementById("cardsServicios");
    const tbody = document.getElementById("tbodyServicios");

    if (cards) {
      pintarCardsCliente();
      const servicios = leerServicios().filter((s) => s.estado === "Activo");
      const empty = document.getElementById("servicios-empty");
      if (empty) empty.classList.toggle("d-none", servicios.length > 0);
    }
    if (tbody) {
      configurarEventosAdmin();
      pintarTablaAdmin();
    }
  }

  function destinoTrasGuardar(usuario) {
    const fk = usuario.rolFk;
    const esStaff = fk === 1 || fk === 2 || usuario.rol === "admin";
    return esStaff ? pageRoute("servicios-admin") : pageRoute("servicios");
  }

  function initFormularioServicio(usuario) {
    const form = document.getElementById("formServicio");
    if (!form) return;

    const id = getIdFromUrl();

    if (id) {
      const servicios = leerServicios();
      const s = servicios.find((x) => x.id === id);
      if (s) {
        const titulo = document.getElementById("tituloFormulario");
        const bread = document.getElementById("breadcrumbAccion");
        if (titulo) titulo.textContent = "Editar servicio";
        if (bread) bread.textContent = "Editar servicio";

        document.getElementById("nombre").value = s.nombre || "";
        document.getElementById("estado").value = s.estado || "Activo";
        document.getElementById("descripcion").value = s.descripcion || "";
        document.getElementById("precio").value = s.precio ?? 0;
        document.getElementById("duracionMin").value = s.duracionMin ?? 30;
        document.getElementById("icono").value = s.icono || "🐾";
      }
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();

      if (typeof apiPostJson !== "function" || typeof apiPutJson !== "function") {
        mostrarErrores("alertaServicio", ["Carga api.js"]);
        return;
      }

      const data = {
        nombre: document.getElementById("nombre").value.trim(),
        estado: document.getElementById("estado").value,
        descripcion: document.getElementById("descripcion").value.trim(),
        precio: Number(document.getElementById("precio").value),
        duracionMin: Number(document.getElementById("duracionMin").value),
        icono: document.getElementById("icono").value,
      };

      const errores = [];
      if (!data.nombre) errores.push("Pon un nombre");
      if (!data.descripcion) errores.push("Pon una descripción");
      if (Number.isNaN(data.precio) || data.precio < 0) errores.push("El precio no puede ser negativo");
      if (Number.isNaN(data.duracionMin) || data.duracionMin <= 0) errores.push("La duración debe ser mayor a 0");

      mostrarErrores("alertaServicio", errores);
      if (errores.length) return;

      const body = {
        nombre: data.nombre,
        estado: data.estado,
        descripcion: data.descripcion,
        precio: data.precio,
        duracionMin: data.duracionMin,
        icono: data.icono,
      };

      const req = id
        ? apiPutJson("api/servicios.php", Object.assign({ servicioId: id }, body))
        : apiPostJson("api/servicios.php", body);

      req
        .then(function (res) {
          if (res && res.ok) {
            mostrarExito("alertaServicio", id ? "Servicio actualizado en la base de datos." : "Servicio creado en la base de datos.");
            return cargarServiciosDesdeApi().then(function () {
              window.setTimeout(function () {
                window.location.href = destinoTrasGuardar(usuario);
              }, 700);
            });
          }
          mostrarErrores("alertaServicio", [res && res.error ? res.error : "No se pudo guardar"]);
        })
        .catch(function () {
          mostrarErrores("alertaServicio", ["Error de red al guardar"]);
        });
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    void (async () => {
      await cargarServiciosDesdeApi();

      const usuario = leerUsuarioActivo();

      initVistaServiciosPorRol(usuario);
      initFormularioServicio(usuario);
    })();
  });
})();
