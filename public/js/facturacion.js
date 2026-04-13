/**
 * Facturas y pagos — api/facturas.php y api/pagos.php
 */
(function () {
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

  function badgeEstadoPago(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "exitoso") return `<span class="badge bg-success">Exitoso</span>`;
    if (e === "pendiente") return `<span class="badge bg-warning text-dark">Pendiente</span>`;
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
    if (!titulo) return;
    if (usuario.rol === "admin") {
      titulo.textContent = "Facturas";
    } else {
      titulo.textContent = "Mis facturas";
    }
  }

  function initFacturas() {
    const tbody = document.getElementById("tbodyFacturas");
    if (!tbody || typeof apiGetJson !== "function") return;

    const usuario = leerUsuarioActivo();
    pintarTextosPorRol(usuario);

    apiGetJson("api/facturas.php")
      .then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML = `<tr><td colspan="7" class="text-danger">Error al cargar facturas</td></tr>`;
          return;
        }
        const todas = data.facturas || [];

        function pintar() {
          const filtros = obtenerFiltrosFacturas();
          const filtradas = aplicarFiltrosFacturas(todas, filtros);
          const resumen = document.getElementById("txtResumenFacturas");
          tbody.innerHTML = "";
          if (filtradas.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted py-4">No hay facturas</td></tr>`;
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
                <a class="btn btn-sm btn-outline-primary" href="${pageRoute("factura-detalle", { id: f.id })}">Ver detalle</a>
              </td>`;
            tbody.appendChild(tr);
          });
          if (resumen) resumen.textContent = `Mostrando ${filtradas.length} factura(s)`;
        }

        const txt = document.getElementById("txtBuscarFactura");
        const sel = document.getElementById("selEstadoFactura");
        const btn = document.getElementById("btnLimpiarFiltrosFactura");
        if (txt) txt.addEventListener("input", pintar);
        if (sel) sel.addEventListener("change", pintar);
        if (btn) {
          btn.addEventListener("click", () => {
            if (txt) txt.value = "";
            if (sel) sel.value = "Todos";
            pintar();
          });
        }
        pintar();
      })
      .catch(() => {
        tbody.innerHTML = `<tr><td colspan="7" class="text-danger">Sin conexión o sesión</td></tr>`;
      });
  }

  function getParam(nombre) {
    return new URLSearchParams(window.location.search).get(nombre);
  }

  function initDetalleFactura() {
    const target = document.getElementById("txtFacturaId");
    if (!target || typeof apiGetJson !== "function") return;

    const id = getParam("id");
    if (!id) {
      const cont = document.getElementById("alertaDetalleFactura");
      if (cont) cont.innerHTML = `<div class="alert alert-danger">Falta el id de factura</div>`;
      return;
    }

    apiGetJson("api/facturas.php?id=" + encodeURIComponent(id))
      .then((data) => {
        if (!data || !data.ok || !data.factura) {
          const cont = document.getElementById("alertaDetalleFactura");
          if (cont) cont.innerHTML = `<div class="alert alert-danger">${(data && data.error) || "No encontrada"}</div>`;
          return;
        }
        const factura = data.factura;
        const elId = document.getElementById("txtFacturaId");
        if (elId) elId.textContent = factura.id;
        const fF = document.getElementById("txtFacturaFecha");
        if (fF) fF.textContent = factura.fecha;
        const fM = document.getElementById("txtFacturaMascota");
        if (fM) fM.textContent = factura.mascota;
        const fC = document.getElementById("txtFacturaCliente");
        if (fC) fC.textContent = factura.clienteNombre;

        const badge = document.getElementById("badgeEstadoFactura");
        if (badge) badge.innerHTML = badgeEstadoFactura(factura.estado);

        const tbody = document.getElementById("tbodyFacturaItems");
        if (tbody) {
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
              <td class="text-end">${formatearColones(sub)}</td>`;
            tbody.appendChild(tr);
          });
          const total = factura.total != null ? factura.total : totalCalc;
          const tEl = document.getElementById("txtFacturaTotal");
          if (tEl) tEl.textContent = formatearColones(total);
          const rE = document.getElementById("txtResumenEstado");
          if (rE) rE.textContent = factura.estado;
          const rC = document.getElementById("txtResumenCantidad");
          if (rC) rC.textContent = String((factura.items || []).length);
          const rT = document.getElementById("txtResumenTotal");
          if (rT) rT.textContent = formatearColones(total);
        }

        const btnPagos = document.getElementById("btnIrPagos");
        if (btnPagos) btnPagos.href = pageRoute("pagos", { factura: factura.id });
      })
      .catch(() => {
        const cont = document.getElementById("alertaDetalleFactura");
        if (cont) cont.innerHTML = `<div class="alert alert-danger">Error al cargar</div>`;
      });
  }

  function obtenerFiltrosPagos() {
    const txt = document.getElementById("txtBuscarPago")?.value.trim().toLowerCase() || "";
    const estado = document.getElementById("selEstadoPago")?.value || "Todos";
    return { txt, estado };
  }

  function aplicarFiltrosPagos(lista, filtros) {
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

  function initPagos() {
    const tbody = document.getElementById("tbodyPagos");
    if (!tbody || typeof apiGetJson !== "function") return;

    const usuario = leerUsuarioActivo();
    const titulo = document.getElementById("tituloPagos");
    if (titulo) {
      titulo.textContent = usuario.rol === "admin" ? "Pagos" : "Mis pagos";
    }

    const facturaParam = getParam("factura");

    apiGetJson("api/pagos.php")
      .then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error</td></tr>`;
          return;
        }
        let pagos = data.pagos || [];
        if (facturaParam) {
          pagos = pagos.filter((p) => p.facturaId === facturaParam);
        }

        function pintar() {
          const filtros = obtenerFiltrosPagos();
          const filtrados = aplicarFiltrosPagos(pagos, filtros);
          const resumen = document.getElementById("txtResumenPagos");
          tbody.innerHTML = "";
          if (filtrados.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-muted">No hay pagos</td></tr>`;
            if (resumen) resumen.textContent = "Mostrando 0 pagos";
            return;
          }
          filtrados.forEach((p) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td class="fw-semibold">${p.id}</td>
              <td><a class="text-decoration-none" href="${pageRoute("factura-detalle", { id: p.facturaId })}">${p.facturaId}</a></td>
              <td>${p.fecha}</td>
              <td>${formatearColones(p.monto)}</td>
              <td>${p.metodo}</td>
              <td>${badgeEstadoPago(p.estado)}</td>`;
            tbody.appendChild(tr);
          });
          if (resumen) resumen.textContent = `Mostrando ${filtrados.length} pago(s)`;
        }

        const txt = document.getElementById("txtBuscarPago");
        const sel = document.getElementById("selEstadoPago");
        const btn = document.getElementById("btnLimpiarFiltrosPago");
        if (txt) txt.addEventListener("input", pintar);
        if (sel) sel.addEventListener("change", pintar);
        if (btn) {
          btn.addEventListener("click", () => {
            if (txt) txt.value = "";
            if (sel) sel.value = "Todos";
            pintar();
          });
        }
        pintar();
      })
      .catch(() => {
        tbody.innerHTML = `<tr><td colspan="6" class="text-danger">Error de conexión</td></tr>`;
      });

    const ayuda = document.getElementById("txtAyudaPago");
    if (ayuda && facturaParam) ayuda.textContent = "Factura: " + facturaParam;
  }

  document.addEventListener("DOMContentLoaded", () => {
    if (document.getElementById("tbodyFacturas")) initFacturas();
    if (document.getElementById("txtFacturaId")) initDetalleFactura();
    if (document.getElementById("tbodyPagos")) initPagos();
  });
})();
