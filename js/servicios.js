// /js/servicios.js
// servicios para patitas
// se usan datos quemados y localStorage para que se vea real sin backend

(function () {
  const STORAGE_KEY = "patitas_servicios";

  const serviciosHardcoded = [
    {
      id: "SRV-001",
      nombre: "Consultas",
      descripcion: "Atencion medica general",
      precio: 15000,
      duracionMin: 30,
      estado: "Activo",
      icono: "🩺",
    },
    {
      id: "SRV-002",
      nombre: "Vacunacion",
      descripcion: "Programa completo de vacunas",
      precio: 12000,
      duracionMin: 20,
      estado: "Activo",
      icono: "💉",
    },
    {
      id: "SRV-003",
      nombre: "Bano",
      descripcion: "Bano con shampoo y secado completo",
      precio: 10000,
      duracionMin: 40,
      estado: "Activo",
      icono: "🛁",
    },
    {
      id: "SRV-004",
      nombre: "Corte de unas",
      descripcion: "Recorte y limpieza basica de patitas",
      precio: 6000,
      duracionMin: 20,
      estado: "Activo",
      icono: "🐾",
    },
    {
      id: "SRV-005",
      nombre: "Farmacia",
      descripcion: "Medicamentos y suplementos",
      precio: 0,
      duracionMin: 10,
      estado: "Inactivo",
      icono: "💊",
    },
  ];

  function leerServicios() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [...serviciosHardcoded];

    try {
      const data = JSON.parse(raw);
      if (!Array.isArray(data)) return [...serviciosHardcoded];
      return data;
    } catch (e) {
      return [...serviciosHardcoded];
    }
  }

  function guardarServicios(lista) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));
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
    if (!raw) return { nombre: "Invitado", rol: "cliente" };

    try {
      const u = JSON.parse(raw);
      return {
        nombre: u.nombre || "Invitado",
        rol: (u.rol || "cliente").toLowerCase(),
      };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente" };
    }
  }

  function ponerUsuarioEnNavbar(usuario) {
    const nombre = document.getElementById("txtNombreUsuario");
    const rol = document.getElementById("txtRolUsuario");
    if (nombre) nombre.textContent = usuario.nombre;
    if (rol) rol.textContent = usuario.rol === "admin" ? "Admin" : "Cliente";
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
    col.className = "col-12 col-md-6 col-lg-5";

    col.innerHTML = `
      <a class="text-decoration-none text-dark"
         href="citas.html?servicio=${encodeURIComponent(s.id)}">

        <div class="card shadow-sm border-0 h-100 card-servicio" style="border-radius:18px;">
          <div class="card-body d-flex gap-3 align-items-center p-4">

            <div class="rounded-circle d-flex align-items-center justify-content-center"
                 style="width:64px;height:64px;background:#A5D6A7;color:#1B5E20;">
              <span style="font-size:26px;">${s.icono || "🐾"}</span>
            </div>

            <div class="flex-grow-1">
              <h3 class="h5 fw-semibold mb-1">${s.nombre}</h3>
              <p class="text-muted mb-0">${s.descripcion}</p>
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
             href="servicio-formulario.html?id=${encodeURIComponent(s.id)}">
            Editar
          </a>
          <button class="btn btn-sm btn-outline-danger ms-2"
                  data-accion="eliminar"
                  data-id="${s.id}">
            Eliminar
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

    const ok = confirm("Seguro que queres eliminar " + serv.nombre);
    if (!ok) return;

    const nuevos = servicios.filter((s) => s.id !== id);
    guardarServicios(nuevos);
    pintarTablaAdmin();
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
    const seccionCliente = document.getElementById("seccionCliente");
    const seccionAdmin = document.getElementById("seccionAdmin");

    if (!seccionCliente && !seccionAdmin) return;

    if (usuario.rol === "admin") {
      if (seccionCliente) seccionCliente.classList.add("d-none");
      if (seccionAdmin) seccionAdmin.classList.remove("d-none");

      configurarEventosAdmin();
      pintarTablaAdmin();
    } else {
      if (seccionAdmin) seccionAdmin.classList.add("d-none");
      if (seccionCliente) seccionCliente.classList.remove("d-none");

      pintarCardsCliente();
    }
  }

  function initFormularioServicio(usuario) {
    const form = document.getElementById("formServicio");
    if (!form) return;

    ponerUsuarioEnNavbar(usuario);

    const id = getIdFromUrl();

    if (id) {
      const servicios = leerServicios();
      const s = servicios.find((x) => x.id === id);
      if (s) {
        const titulo = document.getElementById("tituloFormulario");
        const subt = document.getElementById("subtituloFormulario");
        if (titulo) titulo.textContent = "Editar Servicio";
        if (subt) subt.textContent = "Cambia lo que ocupes y guarda";

        document.getElementById("nombre").value = s.nombre || "";
        document.getElementById("estado").value = s.estado || "Activo";
        document.getElementById("descripcion").value = s.descripcion || "";
        document.getElementById("precio").value = s.precio ?? 0;
        document.getElementById("duracionMin").value = s.duracionMin ?? 30;
        document.getElementById("icono").value = s.icono || "🐾";
      }
    }

    form.addEventListener("submit", (e) => {
      e.preventDefault();

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
      if (!data.descripcion) errores.push("Pon una descripcion");
      if (Number.isNaN(data.precio) || data.precio < 0) errores.push("El precio no puede ser negativo");
      if (Number.isNaN(data.duracionMin) || data.duracionMin <= 0) errores.push("La duracion debe ser mayor a 0");

      mostrarErrores("alertaServicio", errores);
      if (errores.length) return;

      const servicios = leerServicios();

      if (id) {
        const idx = servicios.findIndex((x) => x.id === id);
        if (idx >= 0) servicios[idx] = { ...servicios[idx], ...data };
      } else {
        servicios.push({
          id: "SRV-" + String(Date.now()).slice(-6),
          ...data,
        });
      }

      guardarServicios(servicios);
      mostrarExito("alertaServicio", "Listo servicio guardado");

      setTimeout(() => {
        window.location.href = "servicios.html";
      }, 600);
    });

    if (usuario.rol !== "admin") {
      mostrarExito("alertaServicio", "Vista de prueba el admin normalmente es quien crea servicios");
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) guardarServicios([...serviciosHardcoded]);

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    initVistaServiciosPorRol(usuario);
    initFormularioServicio(usuario);
  });
})();