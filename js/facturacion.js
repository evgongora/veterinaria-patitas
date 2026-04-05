/**
 * Veterinaria Patitas - Facturación
 */
// facturacion para patitas
// esto es para avanzar sin backend usando datos quemados

(function () {
  const FACTURAS_KEY = "patitas_facturas";
  const PAGOS_KEY = "patitas_pagos";

  const facturasHardcoded = [
    {
      id: "FAC-001",
      fecha: "2026-03-04",
      clienteEmail: "cliente@patitas.com",
      clienteNombre: "Juan Perez",
      mascota: "Max",
      estado: "Pendiente",
      total: 27000,
      items: [
        { servicio: "Consultas", cantidad: 1, precio: 15000 },
        { servicio: "Vacunacion", cantidad: 1, precio: 12000 },
      ],
    },
    {
      id: "FAC-002",
      fecha: "2026-03-02",
      clienteEmail: "cliente@patitas.com",
      clienteNombre: "Juan Perez",
      mascota: "Luna",
      estado: "Pagada",
      total: 10000,
      items: [
        { servicio: "Bano", cantidad: 1, precio: 10000 },
      ],
    },
    {
      id: "FAC-003",
      fecha: "2026-03-01",
      clienteEmail: "otro@patitas.com",
      clienteNombre: "Maria Lopez",
      mascota: "Rocky",
      estado: "Cancelada",
      total: 6000,
      items: [
        { servicio: "Corte de unas", cantidad: 1, precio: 6000 },
      ],
    },
  ];

  const pagosHardcoded = [
    {
      id: "PAG-001",
      facturaId: "FAC-002",
      clienteEmail: "cliente@patitas.com",
      monto: 10000,
      fecha: "2026-03-02",
      metodo: "Tarjeta",
      estado: "Exitoso",
    },
    {
      id: "PAG-002",
      facturaId: "FAC-001",
      clienteEmail: "cliente@patitas.com",
      monto: 27000,
      fecha: "2026-03-04",
      metodo: "Sinpe",
      estado: "Pendiente",
    },
  ];

  function seedSiHaceFalta() {
    if (!localStorage.getItem(FACTURAS_KEY)) {
      localStorage.setItem(FACTURAS_KEY, JSON.stringify(facturasHardcoded));
    }
    if (!localStorage.getItem(PAGOS_KEY)) {
      localStorage.setItem(PAGOS_KEY, JSON.stringify(pagosHardcoded));
    }
  }

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente", email: "" };

    try {
      const u = JSON.parse(raw);
      return {
        nombre: u.nombre || "Invitado",
        rol: (u.rol || "cliente").toLowerCase(),
        email: (u.email || "").toLowerCase(),
      };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente", email: "" };
    }
  }

  function ponerUsuarioEnNavbar(usuario) {
    const nombre = document.getElementById("txtNombreUsuario");
    const rol = document.getElementById("txtRolUsuario");
    if (nombre) nombre.textContent = usuario.nombre;
    if (rol) rol.textContent = usuario.rol === "admin" ? "Admin" : "Cliente";
  }

  function leerFacturas() {
    const raw = localStorage.getItem(FACTURAS_KEY);
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function formatearColones(monto) {
    return "₡" + Number(monto || 0).toLocaleString("es-CR");
  }

  function badgeEstadoFactura(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "pagada") return `<span class="badge bg-success">Pagada</span>`;
    if (e === "pendiente") return `<span class="badge bg-warning text-dark">Pendiente</span>`;
    if (e === "cancelada") return `<span class="badge bg-danger">Cancelada</span>`;
    return `<span class="badge bg-secondary">${estado}</span>`;
  }

  function obtenerFiltrosFacturas() {
    const txt = document.getElementById("txtBuscarFactura")?.value.trim().toLowerCase() || "";
    const estado = document.getElementById("selEstadoFactura")?.value || "Todos";
    return { txt, estado };
  }

  function aplicarFiltrosFacturas(lista, filtros) {
    let res = [...lista];

    if (filtros.txt) {
      res = res.filter((f) => {
        const todo = `${f.id} ${f.clienteNombre} ${f.mascota} ${f.estado}`.toLowerCase();
        return todo.includes(filtros.txt);
      });
    }

    if (filtros.estado !== "Todos") {
      res = res.filter((f) => f.estado === filtros.estado);
    }

    return res;
  }

  function pintarTextosPorRol(usuario) {
    const titulo = document.getElementById("tituloFacturas");
    const subt = document.getElementById("subtituloFacturas");

    if (!titulo || !subt) return;

    if (usuario.rol === "admin") {
      titulo.textContent = "Facturas";
      subt.textContent = "Lista general de facturas del sistema";
    } else {
      titulo.textContent = "Mis Facturas";
      subt.textContent = "Revisa tus facturas y su estado";
    }
  }

  function pintarTablaFacturas(usuario) {
    const tbody = document.getElementById("tbodyFacturas");
    const resumen = document.getElementById("txtResumenFacturas");
    if (!tbody) return;

    const todas = leerFacturas();

    let visibles = todas;
    if (usuario.rol !== "admin") {
      visibles = todas.filter((f) => (f.clienteEmail || "").toLowerCase() === usuario.email);
    }

    const filtros = obtenerFiltrosFacturas();
    const filtradas = aplicarFiltrosFacturas(visibles, filtros);

    tbody.innerHTML = "";

    if (filtradas.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center text-muted py-4">
            No hay facturas con esos filtros
          </td>
        </tr>
      `;
      if (resumen) resumen.textContent = "Mostrando 0 facturas";
      return;
    }

    filtradas.forEach((f) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td class="fw-semibold">${f.id}</td>
        <td>${f.fecha}</td>
        <td class="d-none d-md-table-cell">${f.clienteNombre}</td>
        <td class="d-none d-md-table-cell">${f.mascota}</td>
        <td>${formatearColones(f.total)}</td>
        <td>${badgeEstadoFactura(f.estado)}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary" href="${pageRoute('factura-detalle', { id: f.id })}">
            Ver detalle
          </a>
        </td>
      `;

      tbody.appendChild(tr);
    });

    if (resumen) {
      resumen.textContent = `Mostrando ${filtradas.length} factura(s)`;
    }
  }

  function configurarEventosFacturas(usuario) {
    const txt = document.getElementById("txtBuscarFactura");
    const sel = document.getElementById("selEstadoFactura");
    const btn = document.getElementById("btnLimpiarFiltrosFactura");

    if (txt) txt.addEventListener("input", () => pintarTablaFacturas(usuario));
    if (sel) sel.addEventListener("change", () => pintarTablaFacturas(usuario));

    if (btn) {
      btn.addEventListener("click", () => {
        if (txt) txt.value = "";
        if (sel) sel.value = "Todos";
        pintarTablaFacturas(usuario);
      });
    }
  }

  document.addEventListener("DOMContentLoaded", () => {
    seedSiHaceFalta();

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    pintarTextosPorRol(usuario);
    configurarEventosFacturas(usuario);
    pintarTablaFacturas(usuario);
  });
  function getParam(nombre) {
    const p = new URLSearchParams(window.location.search);
    return p.get(nombre);
  }

  function formatearColones(monto) {
    return "₡" + Number(monto || 0).toLocaleString("es-CR");
  }

  function badgeEstadoFactura(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "pagada") return `<span class="badge bg-success">Pagada</span>`;
    if (e === "pendiente") return `<span class="badge bg-warning text-dark">Pendiente</span>`;
    if (e === "cancelada") return `<span class="badge bg-danger">Cancelada</span>`;
    return `<span class="badge bg-secondary">${estado}</span>`;
  }

  function leerFacturas() {
    const raw = localStorage.getItem("patitas_facturas");
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente", email: "" };

    try {
      const u = JSON.parse(raw);
      return {
        nombre: u.nombre || "Invitado",
        rol: (u.rol || "cliente").toLowerCase(),
        email: (u.email || "").toLowerCase(),
      };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente", email: "" };
    }
  }

  function ponerUsuarioEnNavbar(usuario) {
    const nombre = document.getElementById("txtNombreUsuario");
    const rol = document.getElementById("txtRolUsuario");
    if (nombre) nombre.textContent = usuario.nombre;
    if (rol) rol.textContent = usuario.rol === "admin" ? "Admin" : "Cliente";
  }

  function mostrarError(msg) {
    const cont = document.getElementById("alertaDetalleFactura");
    if (!cont) return;
    cont.innerHTML = `<div class="alert alert-danger">${msg}</div>`;
  }

  function pintarDetalleFactura() {
    const target = document.getElementById("txtFacturaId");
    if (!target) return;

    const id = getParam("id");
    if (!id) {
      mostrarError("No viene el id de la factura");
      return;
    }

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    const facturas = leerFacturas();
    const factura = facturas.find((f) => f.id === id);

    if (!factura) {
      mostrarError("No se encontro la factura");
      return;
    }

    if (usuario.rol !== "admin" && (factura.clienteEmail || "").toLowerCase() !== usuario.email) {
      mostrarError("No puedes ver esta factura");
      return;
    }

    document.getElementById("txtFacturaId").textContent = factura.id;
    document.getElementById("txtFacturaFecha").textContent = factura.fecha;
    document.getElementById("txtFacturaMascota").textContent = factura.mascota;
    document.getElementById("txtFacturaCliente").textContent = factura.clienteNombre;

    const badge = document.getElementById("badgeEstadoFactura");
    if (badge) badge.innerHTML = badgeEstadoFactura(factura.estado);

    const tbody = document.getElementById("tbodyFacturaItems");
    tbody.innerHTML = "";

    let totalCalc = 0;

    (factura.items || []).forEach((it) => {
      const cant = Number(it.cantidad || 0);
      const precio = Number(it.precio || 0);
      const sub = cant * precio;
      totalCalc += sub;

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="fw-semibold">${it.servicio}</td>
        <td class="text-center">${cant}</td>
        <td class="text-end">${formatearColones(precio)}</td>
        <td class="text-end">${formatearColones(sub)}</td>
      `;
      tbody.appendChild(tr);
    });

    const total = factura.total != null ? factura.total : totalCalc;

    document.getElementById("txtFacturaTotal").textContent = formatearColones(total);
    document.getElementById("txtResumenEstado").textContent = factura.estado;
    document.getElementById("txtResumenCantidad").textContent = String((factura.items || []).length);
    document.getElementById("txtResumenTotal").textContent = formatearColones(total);

    const btnPagos = document.getElementById("btnIrPagos");
    if (btnPagos) btnPagos.href = pageRoute('pagos', { factura: factura.id });
  }

  document.addEventListener("DOMContentLoaded", pintarDetalleFactura);

  function getParam(nombre) {
    const p = new URLSearchParams(window.location.search);
    return p.get(nombre);
  }

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente", email: "" };

    try {
      const u = JSON.parse(raw);
      return {
        nombre: u.nombre || "Invitado",
        rol: (u.rol || "cliente").toLowerCase(),
        email: (u.email || "").toLowerCase(),
      };
    } catch (e) {
      return { nombre: "Invitado", rol: "cliente", email: "" };
    }
  }

  function ponerUsuarioEnNavbar(usuario) {
    const nombre = document.getElementById("txtNombreUsuario");
    const rol = document.getElementById("txtRolUsuario");
    if (nombre) nombre.textContent = usuario.nombre;
    if (rol) rol.textContent = usuario.rol === "admin" ? "Admin" : "Cliente";
  }

  function leerPagos() {
    const raw = localStorage.getItem(PAGOS_KEY);
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function guardarPagos(lista) {
    localStorage.setItem(PAGOS_KEY, JSON.stringify(lista));
  }

  function leerFacturas() {
    const raw = localStorage.getItem(FACTURAS_KEY);
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function formatearColones(monto) {
    return "₡" + Number(monto || 0).toLocaleString("es-CR");
  }

  function badgeEstadoPago(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "exitoso") return `<span class="badge bg-success">Exitoso</span>`;
    if (e === "pendiente") return `<span class="badge bg-warning text-dark">Pendiente</span>`;
    if (e === "fallido") return `<span class="badge bg-danger">Fallido</span>`;
    return `<span class="badge bg-secondary">${estado}</span>`;
  }

  function mostrarInfo(msg) {
    const cont = document.getElementById("alertaPagos");
    if (!cont) return;
    cont.innerHTML = `<div class="alert alert-info">${msg}</div>`;
  }

  function mostrarExito(msg) {
    const cont = document.getElementById("alertaPagos");
    if (!cont) return;
    cont.innerHTML = `<div class="alert alert-success">${msg}</div>`;
  }

  function pintarTextosPorRol(usuario) {
    const titulo = document.getElementById("tituloPagos");
    const subt = document.getElementById("subtituloPagos");

    if (!titulo || !subt) return;

    if (usuario.rol === "admin") {
      titulo.textContent = "Pagos";
      subt.textContent = "Lista general de pagos del sistema";
    } else {
      titulo.textContent = "Mis Pagos";
      subt.textContent = "Historial de pagos y su estado";
    }
  }

  function obtenerFiltros() {
    const txt = document.getElementById("txtBuscarPago")?.value.trim().toLowerCase() || "";
    const estado = document.getElementById("selEstadoPago")?.value || "Todos";
    return { txt, estado };
  }

  function aplicarFiltros(lista, filtros) {
    let res = [...lista];

    if (filtros.txt) {
      res = res.filter((p) => {
        const todo = `${p.id} ${p.facturaId} ${p.metodo} ${p.estado}`.toLowerCase();
        return todo.includes(filtros.txt);
      });
    }

    if (filtros.estado !== "Todos") {
      res = res.filter((p) => p.estado === filtros.estado);
    }

    return res;
  }

  function pintarTablaPagos(usuario) {
    const tbody = document.getElementById("tbodyPagos");
    const resumen = document.getElementById("txtResumenPagos");
    if (!tbody) return;

    const facturaParam = getParam("factura");

    let pagos = leerPagos();

    if (usuario.rol !== "admin") {
      pagos = pagos.filter((p) => (p.clienteEmail || "").toLowerCase() === usuario.email);
    }

    if (facturaParam) {
      pagos = pagos.filter((p) => p.facturaId === facturaParam);
    }

    const filtros = obtenerFiltros();
    const filtrados = aplicarFiltros(pagos, filtros);

    tbody.innerHTML = "";

    if (filtrados.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            No hay pagos con esos filtros
          </td>
        </tr>
      `;
      if (resumen) resumen.textContent = "Mostrando 0 pagos";
      return;
    }

    filtrados.forEach((p) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="fw-semibold">${p.id}</td>
        <td>
          <a class="text-decoration-none" href="${pageRoute('factura-detalle', { id: p.facturaId })}">
            ${p.facturaId}
          </a>
        </td>
        <td>${p.fecha}</td>
        <td>${formatearColones(p.monto)}</td>
        <td>${p.metodo}</td>
        <td>${badgeEstadoPago(p.estado)}</td>
      `;
      tbody.appendChild(tr);
    });

    if (resumen) resumen.textContent = `Mostrando ${filtrados.length} pago(s)`;
  }

  function configurarEventos(usuario) {
    const txt = document.getElementById("txtBuscarPago");
    const sel = document.getElementById("selEstadoPago");
    const btn = document.getElementById("btnLimpiarFiltrosPago");

    if (txt) txt.addEventListener("input", () => pintarTablaPagos(usuario));
    if (sel) sel.addEventListener("change", () => pintarTablaPagos(usuario));

    if (btn) {
      btn.addEventListener("click", () => {
        if (txt) txt.value = "";
        if (sel) sel.value = "Todos";
        pintarTablaPagos(usuario);
      });
    }

    const btnSimular = document.getElementById("btnSimularPago");
    if (btnSimular) {
      btnSimular.addEventListener("click", () => simularPago(usuario));
    }
  }

  function simularPago(usuario) {
    const facturaParam = getParam("factura");
    if (!facturaParam) {
      mostrarInfo("Para simular pago entra desde una factura o agrega ?factura=FAC-001");
      return;
    }

    const facturas = leerFacturas();
    const fac = facturas.find((f) => f.id === facturaParam);

    if (!fac) {
      mostrarInfo("No se encontro la factura para pagar");
      return;
    }

    if (usuario.rol !== "admin" && (fac.clienteEmail || "").toLowerCase() !== usuario.email) {
      mostrarInfo("No puedes pagar una factura que no es tuya");
      return;
    }

    const pagos = leerPagos();

    const nuevoPago = {
      id: "PAG-" + String(Date.now()).slice(-6),
      facturaId: fac.id,
      clienteEmail: fac.clienteEmail,
      monto: fac.total,
      fecha: new Date().toISOString().slice(0, 10),
      metodo: "Sinpe",
      estado: "Exitoso",
    };

    pagos.unshift(nuevoPago);
    guardarPagos(pagos);

    mostrarExito("Listo pago registrado");

    pintarTablaPagos(usuario);
  }

  function pintarAyudaFactura() {
    const facturaParam = getParam("factura");
    const ayuda = document.getElementById("txtAyudaPago");
    if (!ayuda) return;

    if (facturaParam) {
      ayuda.textContent = "Factura seleccionada " + facturaParam;
    } else {
      ayuda.textContent = "Si vienes desde una factura veras el id aqui";
    }
  }

  function initPagos() {
    const tbody = document.getElementById("tbodyPagos");
    if (!tbody) return;

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    pintarTextosPorRol(usuario);
    pintarAyudaFactura();
    configurarEventos(usuario);
    pintarTablaPagos(usuario);
  }

  document.addEventListener("DOMContentLoaded", initPagos);

})();