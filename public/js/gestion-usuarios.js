/**
 * Gestión de usuarios unificada — slider Clientes / Veterinarios + CRUD (solo admin en esta vista).
 */
(function () {
  function escapeHtml(s) {
    return String(s || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function showAlert(el, mensaje, tipo) {
    if (!el) return;
    el.className = "alert alert-" + tipo;
    el.textContent = mensaje;
    el.classList.remove("d-none");
  }

  function hideAlert(el) {
    if (!el) return;
    el.classList.add("d-none");
    el.textContent = "";
  }

  function mostrarError(input, mensaje) {
    if (!input) return;
    input.classList.add("is-invalid");
    const err = document.getElementById(input.id + "-error");
    if (err) err.textContent = mensaje;
  }

  function limpiarError(input) {
    if (!input) return;
    input.classList.remove("is-invalid");
    const err = document.getElementById(input.id + "-error");
    if (err) err.textContent = "";
  }

  const GestionUsuarios = {
    tabActual: "clientes",

    init() {
      const p = new URLSearchParams(window.location.search);
      const t = p.get("tab");
      if (t === "veterinarios") {
        this.tabActual = "veterinarios";
      } else {
        this.tabActual = "clientes";
      }

      document.getElementById("segBtnClientes").addEventListener("click", () => this.setTab("clientes"));
      document.getElementById("segBtnVets").addEventListener("click", () => this.setTab("veterinarios"));

      document.getElementById("btnGuToggleClienteForm").addEventListener("click", () => this.toggleCollapse("cliente"));
      document.getElementById("btnGuToggleVetForm").addEventListener("click", () => this.toggleCollapse("vet"));

      document.getElementById("gu-cliente-cancelar").addEventListener("click", () => this.resetClienteForm(true));
      document.getElementById("gu-vet-cancelar").addEventListener("click", () => this.resetVetForm(true));

      document.getElementById("gu-cliente-form").addEventListener("submit", (e) => this.submitCliente(e));
      document.getElementById("gu-vet-form").addEventListener("submit", (e) => this.submitVet(e));

      const elColC = document.getElementById("collapseFormCliente");
      const elColV = document.getElementById("collapseFormVet");
      if (elColC) {
        elColC.addEventListener("shown.bs.collapse", () => {
          const lbl = document.getElementById("btnGuClienteFormLabel");
          if (lbl) lbl.textContent = "Ocultar formulario";
        });
        elColC.addEventListener("hidden.bs.collapse", () => {
          const lbl = document.getElementById("btnGuClienteFormLabel");
          if (lbl) lbl.textContent = "Nuevo cliente";
        });
      }
      if (elColV) {
        elColV.addEventListener("shown.bs.collapse", () => {
          const lbl = document.getElementById("btnGuVetFormLabel");
          if (lbl) lbl.textContent = "Ocultar formulario";
        });
        elColV.addEventListener("hidden.bs.collapse", () => {
          const lbl = document.getElementById("btnGuVetFormLabel");
          if (lbl) lbl.textContent = "Nuevo veterinario";
        });
      }

      this.setTab(this.tabActual, true);
      this.loadClientes();
      this.loadVeterinarios();
    },

    setTab(tab, skipUrl) {
      this.tabActual = tab === "veterinarios" ? "veterinarios" : "clientes";
      const seg = document.getElementById("patitasSegment");
      const pClientes = document.getElementById("gu-panel-clientes");
      const pVets = document.getElementById("gu-panel-veterinarios");
      const bC = document.getElementById("segBtnClientes");
      const bV = document.getElementById("segBtnVets");

      if (this.tabActual === "clientes") {
        if (seg) seg.setAttribute("data-seg", "0");
        if (pClientes) pClientes.classList.remove("d-none");
        if (pVets) pVets.classList.add("d-none");
        if (bC) {
          bC.classList.add("active");
          bC.setAttribute("aria-selected", "true");
        }
        if (bV) {
          bV.classList.remove("active");
          bV.setAttribute("aria-selected", "false");
        }
      } else {
        if (seg) seg.setAttribute("data-seg", "1");
        if (pClientes) pClientes.classList.add("d-none");
        if (pVets) pVets.classList.remove("d-none");
        if (bC) {
          bC.classList.remove("active");
          bC.setAttribute("aria-selected", "false");
        }
        if (bV) {
          bV.classList.add("active");
          bV.setAttribute("aria-selected", "true");
        }
      }

      if (!skipUrl && typeof pageRoute === "function") {
        const url = pageRoute("gestion-usuarios", {
          tab: this.tabActual === "veterinarios" ? "veterinarios" : "clientes",
        });
        try {
          history.replaceState(null, "", url);
        } catch (e1) {}
      }
    },

    getCollapse(which) {
      const id = which === "vet" ? "collapseFormVet" : "collapseFormCliente";
      const el = document.getElementById(id);
      if (!el || typeof bootstrap === "undefined") return null;
      return bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
    },

    toggleCollapse(which) {
      const c = this.getCollapse(which);
      if (c) c.toggle();
    },

    showFormCliente(show) {
      const el = document.getElementById("collapseFormCliente");
      if (!el || typeof bootstrap === "undefined") return;
      const c = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
      if (show) c.show();
      else c.hide();
      const lbl = document.getElementById("btnGuClienteFormLabel");
      if (lbl) lbl.textContent = show ? "Ocultar formulario" : "Nuevo cliente";
    },

    showFormVet(show) {
      const el = document.getElementById("collapseFormVet");
      if (!el || typeof bootstrap === "undefined") return;
      const c = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
      if (show) c.show();
      else c.hide();
      const lbl = document.getElementById("btnGuVetFormLabel");
      if (lbl) lbl.textContent = show ? "Ocultar formulario" : "Nuevo veterinario";
    },

    resetClienteForm(collapse) {
      const form = document.getElementById("gu-cliente-form");
      if (form) form.reset();
      document.getElementById("gu-cliente-edit-id").value = "";
      document.getElementById("guClienteTituloForm").textContent = "Nuevo cliente";
      const hint = document.getElementById("gu-cliente-password-hint");
      if (hint) hint.textContent = "Mínimo 4 caracteres para nuevos registros.";
      ["gu-cliente-nombre", "gu-cliente-cedula", "gu-cliente-telefono", "gu-cliente-email", "gu-cliente-password"].forEach((id) => {
        const inp = document.getElementById(id);
        if (inp) limpiarError(inp);
      });
      if (collapse) this.showFormCliente(false);
    },

    resetVetForm(collapse) {
      const form = document.getElementById("gu-vet-form");
      if (form) form.reset();
      document.getElementById("gu-vet-edit-id").value = "";
      document.getElementById("guVetTituloForm").textContent = "Nuevo veterinario";
      const hint = document.getElementById("gu-vet-password-hint");
      if (hint) hint.textContent = "Mínimo 4 caracteres para nuevos registros.";
      [
        "gu-vet-nombre",
        "gu-vet-cedula",
        "gu-vet-especialidad",
        "gu-vet-telefono",
        "gu-vet-email",
        "gu-vet-password",
      ].forEach((id) => {
        const inp = document.getElementById(id);
        if (inp) limpiarError(inp);
      });
      if (collapse) this.showFormVet(false);
    },

    loadClientes() {
      const tbody = document.querySelector("#tabla-gu-clientes tbody");
      if (!tbody || typeof apiGetJson !== "function") return;
      apiGetJson("api/clientes.php").then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML =
            '<tr><td colspan="5" class="text-danger">Sin permiso o error al cargar</td></tr>';
          return;
        }
        const list = data.clientes || [];
        if (list.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-muted">No hay clientes</td></tr>';
          return;
        }
        tbody.innerHTML = list
          .map(
            (c) =>
              `<tr>
            <td>${escapeHtml(c.cedula)}</td>
            <td>${escapeHtml(c.nombre)}</td>
            <td>${escapeHtml(c.telefono || "—")}</td>
            <td>${escapeHtml(c.email || "—")}</td>
            <td class="text-end">
              <button type="button" class="btn btn-warning btn-sm btn-gu-edit-cliente" data-id="${c.id}">Editar</button>
            </td>
          </tr>`
          )
          .join("");
        tbody.querySelectorAll(".btn-gu-edit-cliente").forEach((btn) => {
          btn.addEventListener("click", () => this.cargarClienteEdit(btn.getAttribute("data-id")));
        });
      })
        .catch(() => {
          tbody.innerHTML =
            '<tr><td colspan="5" class="text-danger">Error de conexión (¿iniciaste sesión como admin?)</td></tr>';
        });
    },

    cargarClienteEdit(id) {
      if (!id || typeof apiGetJson !== "function") return;
      apiGetJson("api/clientes.php?id=" + encodeURIComponent(id)).then((data) => {
        if (!data || !data.ok || !data.cliente) {
          showAlert(document.getElementById("gu-alert-cliente"), (data && data.error) || "No se pudo cargar.", "danger");
          return;
        }
        const c = data.cliente;
        document.getElementById("gu-cliente-edit-id").value = String(id);
        document.getElementById("guClienteTituloForm").textContent = "Editar cliente";
        document.getElementById("gu-cliente-nombre").value = c.nombre || "";
        document.getElementById("gu-cliente-cedula").value = c.cedula || "";
        document.getElementById("gu-cliente-telefono").value = c.telefono || "";
        document.getElementById("gu-cliente-email").value = c.email || "";
        document.getElementById("gu-cliente-password").value = "";
        const hint = document.getElementById("gu-cliente-password-hint");
        if (hint) hint.textContent = "Dejar vacío para no cambiar la contraseña.";
        this.setTab("clientes", true);
        this.showFormCliente(true);
        document.getElementById("collapseFormCliente").scrollIntoView({ behavior: "smooth", block: "nearest" });
      });
    },

    loadVeterinarios() {
      const tbody = document.querySelector("#tabla-gu-veterinarios tbody");
      if (!tbody || typeof apiGetJson !== "function") return;
      apiGetJson("api/veterinarios.php").then((data) => {
        if (!data || !data.ok) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Error al cargar</td></tr>';
          return;
        }
        const list = data.veterinarios || [];
        if (list.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-muted">No hay veterinarios</td></tr>';
          return;
        }
        tbody.innerHTML = list
          .map(
            (v) =>
              `<tr>
            <td>${v.id}</td>
            <td>${escapeHtml(v.nombreCompleto)}</td>
            <td>${escapeHtml(v.especialidad || "—")}</td>
            <td>${escapeHtml(v.telefono || "—")}</td>
            <td class="text-end">
              <button type="button" class="btn btn-warning btn-sm btn-gu-edit-vet" data-id="${v.id}">Editar</button>
            </td>
          </tr>`
          )
          .join("");
        tbody.querySelectorAll(".btn-gu-edit-vet").forEach((btn) => {
          btn.addEventListener("click", () => this.cargarVetEdit(btn.getAttribute("data-id")));
        });
      })
        .catch(() => {
          tbody.innerHTML = '<tr><td colspan="5" class="text-danger">Error de conexión</td></tr>';
        });
    },

    cargarVetEdit(id) {
      if (!id || typeof apiGetJson !== "function") return;
      apiGetJson("api/veterinarios.php?id=" + encodeURIComponent(id)).then((data) => {
        if (!data || !data.ok || !data.veterinario) {
          showAlert(document.getElementById("gu-alert-vet"), (data && data.error) || "No se pudo cargar.", "danger");
          return;
        }
        const v = data.veterinario;
        document.getElementById("gu-vet-edit-id").value = String(id);
        document.getElementById("guVetTituloForm").textContent = "Editar veterinario";
        document.getElementById("gu-vet-nombre").value = v.nombre || "";
        document.getElementById("gu-vet-cedula").value = v.cedula || "";
        document.getElementById("gu-vet-especialidad").value = v.especialidad || "";
        document.getElementById("gu-vet-telefono").value = v.telefono || "";
        document.getElementById("gu-vet-email").value = v.email || "";
        document.getElementById("gu-vet-password").value = "";
        const hint = document.getElementById("gu-vet-password-hint");
        if (hint) hint.textContent = "Dejar vacío para no cambiar la contraseña.";
        this.setTab("veterinarios", true);
        this.showFormVet(true);
        document.getElementById("collapseFormVet").scrollIntoView({ behavior: "smooth", block: "nearest" });
      });
    },

    async submitCliente(e) {
      e.preventDefault();
      const alertEl = document.getElementById("gu-alert-cliente");
      hideAlert(alertEl);

      const editId = document.getElementById("gu-cliente-edit-id").value.trim();
      const nombre = document.getElementById("gu-cliente-nombre").value.trim();
      const cedula = document.getElementById("gu-cliente-cedula").value.trim();
      const telefono = document.getElementById("gu-cliente-telefono").value.trim();
      const email = document.getElementById("gu-cliente-email").value.trim();
      const password = document.getElementById("gu-cliente-password").value || "";

      const campos = [
        { id: "gu-cliente-nombre", val: Validaciones.requerido(nombre, "Nombre completo") },
        { id: "gu-cliente-cedula", val: Validaciones.requerido(cedula, "Cédula") },
        { id: "gu-cliente-telefono", val: Validaciones.requerido(telefono, "Teléfono") },
        { id: "gu-cliente-email", val: Validaciones.email(email) },
      ];

      let hayError = false;
      campos.forEach((c) => {
        if (!c.val.valido) {
          mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          limpiarError(document.getElementById(c.id));
        }
      });

      if (!editId && password.length < 4) {
        mostrarError(document.getElementById("gu-cliente-password"), "La contraseña debe tener al menos 4 caracteres");
        hayError = true;
      } else {
        limpiarError(document.getElementById("gu-cliente-password"));
      }

      if (hayError) return;

      const body = { nombre, cedula, telefono, email };
      if (password !== "") body.password = password;

      try {
        let data;
        if (editId) {
          body.clienteId = parseInt(editId, 10);
          data = await apiPutJson("api/clientes.php", body);
        } else {
          body.password = password;
          data = await apiPostJson("api/clientes.php", body);
        }
        if (data && data.ok) {
          showAlert(alertEl, editId ? "Cliente actualizado." : "Cliente creado.", "success");
          this.resetClienteForm(true);
          this.loadClientes();
        } else {
          showAlert(alertEl, (data && data.error) || "No se pudo guardar.", "danger");
        }
      } catch (err) {
        showAlert(alertEl, "Error de red.", "danger");
      }
    },

    async submitVet(e) {
      e.preventDefault();
      const alertEl = document.getElementById("gu-alert-vet");
      hideAlert(alertEl);

      const editId = document.getElementById("gu-vet-edit-id").value.trim();
      const nombre = document.getElementById("gu-vet-nombre").value.trim();
      const cedula = document.getElementById("gu-vet-cedula").value.trim();
      const especialidad = document.getElementById("gu-vet-especialidad").value;
      const telefono = document.getElementById("gu-vet-telefono").value.trim();
      const email = document.getElementById("gu-vet-email").value.trim();
      const password = document.getElementById("gu-vet-password").value || "";

      const campos = [
        { id: "gu-vet-nombre", val: Validaciones.requerido(nombre, "Nombre completo") },
        { id: "gu-vet-cedula", val: Validaciones.requerido(cedula, "Cédula") },
        { id: "gu-vet-especialidad", val: Validaciones.requerido(especialidad, "Especialidad") },
        { id: "gu-vet-telefono", val: Validaciones.requerido(telefono, "Teléfono") },
        { id: "gu-vet-email", val: Validaciones.email(email) },
      ];

      let hayError = false;
      campos.forEach((c) => {
        if (!c.val.valido) {
          mostrarError(document.getElementById(c.id), c.val.mensaje);
          hayError = true;
        } else {
          limpiarError(document.getElementById(c.id));
        }
      });

      if (!editId && password.length < 4) {
        mostrarError(document.getElementById("gu-vet-password"), "La contraseña debe tener al menos 4 caracteres");
        hayError = true;
      } else {
        limpiarError(document.getElementById("gu-vet-password"));
      }

      if (hayError) return;

      const body = { nombre, cedula, especialidad, telefono, email };
      if (password !== "") body.password = password;

      try {
        let data;
        if (editId) {
          body.veterinarioId = parseInt(editId, 10);
          data = await apiPutJson("api/veterinarios.php", body);
        } else {
          body.password = password;
          data = await apiPostJson("api/veterinarios.php", body);
        }
        if (data && data.ok) {
          showAlert(alertEl, editId ? "Veterinario actualizado." : "Veterinario creado.", "success");
          this.resetVetForm(true);
          this.loadVeterinarios();
        } else {
          showAlert(alertEl, (data && data.error) || "No se pudo guardar.", "danger");
        }
      } catch (err) {
        showAlert(alertEl, "Error de red.", "danger");
      }
    },
  };

  document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("patitasSegment")) {
      GestionUsuarios.init();
    }
  });
})();
