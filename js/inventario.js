/**
 * Veterinaria Patitas - Inventario de medicamentos
 */
// inventario para patitas
// solo admin puede ver esto
// usamos localStorage para simular base de datos

(function () {
  const STORAGE_KEY = "patitas_inventario";

  const inventarioHardcoded = [
    { id: "INV-001", nombre: "Amoxicilina 500mg", cantidad: 45, vencimiento: "2026-09-15", proveedor: "FarmaVet S.A.", estado: "Normal" },
    { id: "INV-002", nombre: "Vacuna Antirrabica", cantidad: 12, vencimiento: "2026-08-30", proveedor: "BioVet Labs", estado: "Bajo" },
    { id: "INV-003", nombre: "Desparasitante Canino", cantidad: 78, vencimiento: "2026-12-20", proveedor: "PetMed Corp", estado: "Normal" },
    { id: "INV-004", nombre: "Antiinflamatorio", cantidad: 5, vencimiento: "2026-05-10", proveedor: "FarmaVet S.A.", estado: "Critico" },
    { id: "INV-005", nombre: "Antibiotico Topico", cantidad: 23, vencimiento: "2026-07-05", proveedor: "MediPet Inc", estado: "Normal" },
    { id: "INV-006", nombre: "Suplemento Vitaminico", cantidad: 8, vencimiento: "2026-06-15", proveedor: "NutriVet", estado: "Bajo" },
  ];

  function seedSiHaceFalta() {
    if (!localStorage.getItem(STORAGE_KEY)) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(inventarioHardcoded));
    }
  }

  function leerUsuarioActivo() {
    const raw = localStorage.getItem("usuarioActivo");
    if (!raw) return { nombre: "Invitado", rol: "cliente" };

    try {
      const u = JSON.parse(raw);
      return { nombre: u.nombre || "Invitado", rol: (u.rol || "cliente").toLowerCase() };
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

  function leerInventario() {
    const raw = localStorage.getItem(STORAGE_KEY);
    if (!raw) return [];
    try {
      const data = JSON.parse(raw);
      return Array.isArray(data) ? data : [];
    } catch (e) {
      return [];
    }
  }

  function guardarInventario(lista) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(lista));
  }

  function estadoBadge(estado) {
    const e = (estado || "").toLowerCase();
    if (e === "normal") return `<span class="badge" style="background:#DCFCE7;color:#166534;border-radius:999px;">Stock Normal</span>`;
    if (e === "bajo") return `<span class="badge" style="background:#FEF3C7;color:#92400E;border-radius:999px;">Stock Bajo</span>`;
    if (e === "critico") return `<span class="badge" style="background:#FEE2E2;color:#B91C1C;border-radius:999px;">Stock Critico</span>`;
    return `<span class="badge bg-secondary">${estado}</span>`;
  }

  function formatearFecha(iso) {
    if (!iso) return "-";
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

    if (elT) elT.textContent = total;
    if (elN) elN.textContent = normal;
    if (elB) elB.textContent = bajo;
    if (elC) elC.textContent = critico;

    const banner = document.getElementById("bannerStock");
    if (!banner) return;

    const alerta = bajo + critico;

    if (alerta > 0) {
      banner.innerHTML = `
        <div class="alert alert-warning d-flex align-items-start gap-3" style="border-left:6px solid #F59E0B;">
          <div class="fs-4">⚠️</div>
          <div>
            <div class="fw-semibold">Atencion ${alerta} medicamentos con stock bajo o critico</div>
            <div class="small">Se recomienda realizar pedido de reposicion</div>
          </div>
        </div>
      `;
    } else {
      banner.innerHTML = "";
    }
  }

  function pintarTabla() {
    const tbody = document.getElementById("tbodyInventario");
    const resumen = document.getElementById("txtResumenInventario");
    if (!tbody) return;

    const lista = leerInventario();

    pintarStats(lista);

    tbody.innerHTML = "";

    if (lista.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center text-muted py-4">
            No hay items en inventario
          </td>
        </tr>
      `;
      if (resumen) resumen.textContent = "Mostrando 0 items";
      return;
    }

    lista.forEach((i) => {
      const tr = document.createElement("tr");

      tr.innerHTML = `
        <td class="fw-semibold">${i.nombre}</td>
        <td>${i.cantidad}</td>
        <td>${formatearFecha(i.vencimiento)}</td>
        <td class="text-muted">${i.proveedor}</td>
        <td>${estadoBadge(i.estado)}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-primary"
             href="inventario-formulario.html?id=${encodeURIComponent(i.id)}">
            Editar
          </a>
          <button class="btn btn-sm btn-outline-danger ms-2"
                  data-accion="eliminar"
                  data-id="${i.id}">
            Eliminar
          </button>
        </td>
      `;

      tbody.appendChild(tr);
    });

    if (resumen) resumen.textContent = `Mostrando ${lista.length} item(s)`;

    tbody.querySelectorAll("button[data-accion='eliminar']").forEach((btn) => {
      btn.addEventListener("click", () => {
        const id = btn.getAttribute("data-id");
        eliminarItem(id);
      });
    });
  }

  function eliminarItem(id) {
    const lista = leerInventario();
    const item = lista.find((x) => x.id === id);
    if (!item) return;

    const ok = confirm("Seguro que queres eliminar " + item.nombre);
    if (!ok) return;

    const nuevo = lista.filter((x) => x.id !== id);
    guardarInventario(nuevo);
    pintarTabla();
  }

  function mostrarSoloAdmin(usuario) {
    const alerta = document.getElementById("alertaSoloAdmin");
    const seccion = document.getElementById("seccionInventario");

    if (usuario.rol !== "admin") {
      if (alerta) {
        alerta.innerHTML = `
          <div class="alert alert-danger">
            Esta pantalla es solo para administracion
          </div>
        `;
      }
      if (seccion) seccion.classList.add("d-none");
      return false;
    }

    if (alerta) alerta.innerHTML = "";
    if (seccion) seccion.classList.remove("d-none");
    return true;
  }

  function initInventario() {
    const tbody = document.getElementById("tbodyInventario");
    if (!tbody) return;

    seedSiHaceFalta();

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    const ok = mostrarSoloAdmin(usuario);
    if (!ok) return;

    pintarTabla();
  }

    function getParam(nombre) {
    const p = new URLSearchParams(window.location.search);
    return p.get(nombre);
  }

  function mostrarErrorForm(lista) {
    const cont = document.getElementById("alertaInventarioForm");
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

  function mostrarExitoForm(msg) {
    const cont = document.getElementById("alertaInventarioForm");
    if (!cont) return;
    cont.innerHTML = `<div class="alert alert-success">${msg}</div>`;
  }

  function mostrarSoloAdminForm(usuario) {
    const alerta = document.getElementById("alertaSoloAdmin");
    const seccion = document.getElementById("seccionFormularioInventario");

    if (!seccion) return false;

    if (usuario.rol !== "admin") {
      if (alerta) {
        alerta.innerHTML = `
          <div class="alert alert-danger">
            Esta pantalla es solo para administracion
          </div>
        `;
      }
      seccion.classList.add("d-none");
      return false;
    }

    if (alerta) alerta.innerHTML = "";
    seccion.classList.remove("d-none");
    return true;
  }

  function cargarSiEdita() {
    const form = document.getElementById("formInventario");
    if (!form) return;

    const id = getParam("id");
    if (!id) return;

    const lista = leerInventario();
    const item = lista.find((x) => x.id === id);
    if (!item) return;

    const titulo = document.getElementById("tituloInventarioForm");
    const subt = document.getElementById("subtituloInventarioForm");
    const btn = document.getElementById("btnGuardarInventario");

    if (titulo) titulo.textContent = "Editar Medicamento";
    if (subt) subt.textContent = "Cambia lo que ocupes y guarda";
    if (btn) btn.textContent = "Guardar Cambios";

    document.getElementById("nombre").value = item.nombre || "";
    document.getElementById("cantidad").value = item.cantidad ?? 0;
    document.getElementById("estado").value = item.estado || "Normal";
    document.getElementById("vencimiento").value = item.vencimiento || "";
    document.getElementById("proveedor").value = item.proveedor || "";
  }

  function initFormularioInventario() {
    const form = document.getElementById("formInventario");
    if (!form) return;

    seedSiHaceFalta();

    const usuario = leerUsuarioActivo();
    ponerUsuarioEnNavbar(usuario);

    const ok = mostrarSoloAdminForm(usuario);
    if (!ok) return;

    cargarSiEdita();

    form.addEventListener("submit", (e) => {
      e.preventDefault();

      const id = getParam("id");

      const data = {
        nombre: document.getElementById("nombre").value.trim(),
        cantidad: Number(document.getElementById("cantidad").value),
        estado: document.getElementById("estado").value,
        vencimiento: document.getElementById("vencimiento").value,
        proveedor: document.getElementById("proveedor").value.trim(),
      };

      const errores = [];
      if (!data.nombre) errores.push("Pon el nombre del medicamento");
      if (Number.isNaN(data.cantidad) || data.cantidad < 0) errores.push("La cantidad no puede ser negativa");
      if (!data.vencimiento) errores.push("Pon la fecha de vencimiento");
      if (!data.proveedor) errores.push("Pon el proveedor");

      mostrarErrorForm(errores);
      if (errores.length) return;

      const lista = leerInventario();

      if (id) {
        const idx = lista.findIndex((x) => x.id === id);
        if (idx >= 0) {
          lista[idx] = { ...lista[idx], ...data, id };
        }
      } else {
        lista.unshift({
          id: "INV-" + String(Date.now()).slice(-6),
          ...data,
        });
      }

      guardarInventario(lista);
      mostrarExitoForm("Listo item guardado");

      setTimeout(() => {
        window.location.href = "inventario.html";
      }, 600);
    });
  }

  document.addEventListener("DOMContentLoaded", () => {
    initInventario();
    initFormularioInventario();
  });
})();