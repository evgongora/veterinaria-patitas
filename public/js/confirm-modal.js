/**
 * Modales Bootstrap 5 — confirmación (dos botones) y aviso (un botón).
 * Requiere bootstrap.bundle antes de este script.
 *
 * patitasConfirmar(opts) → Promise<boolean>
 * patitasAlerta(opts) → Promise<void> (se resuelve al cerrar)
 */
(function (global) {
  var VARIANT_TITLE = {
    success: "text-success",
    danger: "text-danger",
    warning: "text-warning",
    info: "text-primary",
  };

  function ensureModalConfirm() {
    if (document.getElementById("patitasModalConfirm")) return;

    var wrap = document.createElement("div");
    wrap.innerHTML =
      '<div class="modal fade" id="patitasModalConfirm" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="patitasModalConfirmTitle">' +
      '<div class="modal-dialog modal-dialog-centered">' +
      '<div class="modal-content border-0 shadow">' +
      '<div class="modal-header border-0 pb-0">' +
      '<h5 class="modal-title fw-bold" id="patitasModalConfirmTitle">Confirmar</h5>' +
      '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
      "</div>" +
      '<div class="modal-body text-muted pt-0" id="patitasModalConfirmBody"></div>' +
      '<div class="modal-footer border-0 pt-0">' +
      '<button type="button" class="btn btn-outline-secondary" id="patitasModalConfirmCancel">Cancelar</button>' +
      '<button type="button" class="btn btn-primary" id="patitasModalConfirmOk">Aceptar</button>' +
      "</div></div></div></div>";

    document.body.appendChild(wrap.firstElementChild);
  }

  function ensureModalAlert() {
    if (document.getElementById("patitasModalAlert")) return;

    var wrap = document.createElement("div");
    wrap.innerHTML =
      '<div class="modal fade" id="patitasModalAlert" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="patitasModalAlertTitle">' +
      '<div class="modal-dialog modal-dialog-centered">' +
      '<div class="modal-content border-0 shadow">' +
      '<div class="modal-header border-0 pb-0">' +
      '<h5 class="modal-title fw-bold" id="patitasModalAlertTitle">Aviso</h5>' +
      '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
      "</div>" +
      '<div class="modal-body pt-0" id="patitasModalAlertBody"></div>' +
      '<div class="modal-footer border-0 pt-0">' +
      '<button type="button" class="btn btn-primary" id="patitasModalAlertOk">Entendido</button>' +
      "</div></div></div></div>";

    document.body.appendChild(wrap.firstElementChild);
  }

  /**
   * @param {object} opts
   * @param {string} [opts.title]
   * @param {string} [opts.message]
   * @param {string} [opts.confirmLabel]
   * @param {string} [opts.cancelLabel]
   * @param {string} [opts.confirmClass]
   * @returns {Promise<boolean>}
   */
  function patitasConfirmar(opts) {
    opts = opts || {};
    if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
      return Promise.resolve(
        Boolean(window.confirm(opts.message || opts.title || "¿Continuar?"))
      );
    }

    ensureModalConfirm();

    return new Promise(function (resolve) {
      var modalEl = document.getElementById("patitasModalConfirm");
      var titleEl = document.getElementById("patitasModalConfirmTitle");
      var bodyEl = document.getElementById("patitasModalConfirmBody");
      var btnOk = document.getElementById("patitasModalConfirmOk");
      var btnCancel = document.getElementById("patitasModalConfirmCancel");

      if (!modalEl || !btnOk || !btnCancel) {
        resolve(Boolean(window.confirm(opts.message || "¿Continuar?")));
        return;
      }

      if (titleEl) titleEl.textContent = opts.title || "Confirmar";
      if (bodyEl) bodyEl.textContent = opts.message || "";

      btnOk.textContent = opts.confirmLabel || "Aceptar";
      btnOk.className = "btn " + (opts.confirmClass || "btn-primary");

      btnCancel.textContent = opts.cancelLabel || "Cancelar";

      var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
        backdrop: true,
        keyboard: true,
      });

      var result = null;

      function onHidden() {
        resolve(result === true);
      }

      function onOk() {
        result = true;
        modal.hide();
      }

      function onCancel() {
        result = false;
        modal.hide();
      }

      modalEl.addEventListener("hidden.bs.modal", onHidden, { once: true });
      btnOk.addEventListener("click", onOk, { once: true });
      btnCancel.addEventListener("click", onCancel, { once: true });

      modal.show();
    });
  }

  /**
   * Modal de un solo botón (sustituye alert()).
   * @param {object} opts
   * @param {string} [opts.title]
   * @param {string} [opts.message]
   * @param {'success'|'danger'|'warning'|'info'} [opts.variant]
   * @param {string} [opts.buttonLabel]
   * @param {string} [opts.buttonClass]
   * @returns {Promise<void>}
   */
  function patitasAlerta(opts) {
    opts = opts || {};
    var msg = opts.message || "";
    var title = opts.title || "Aviso";
    var variant = opts.variant || "info";

    if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
      window.alert(title + (msg ? "\n\n" + msg : ""));
      return Promise.resolve();
    }

    ensureModalAlert();

    return new Promise(function (resolve) {
      var modalEl = document.getElementById("patitasModalAlert");
      var titleEl = document.getElementById("patitasModalAlertTitle");
      var bodyEl = document.getElementById("patitasModalAlertBody");
      var btnOk = document.getElementById("patitasModalAlertOk");

      if (!modalEl || !btnOk) {
        window.alert(title + (msg ? "\n\n" + msg : ""));
        resolve();
        return;
      }

      if (titleEl) {
        titleEl.textContent = title;
        titleEl.className =
          "modal-title fw-bold " + (VARIANT_TITLE[variant] || "text-dark");
      }
      if (bodyEl) {
        bodyEl.className =
          "modal-body pt-0 " + (variant === "danger" || variant === "warning" ? "text-body" : "text-muted");
        bodyEl.textContent = msg;
      }

      btnOk.textContent = opts.buttonLabel || "Entendido";
      btnOk.className = "btn " + (opts.buttonClass || "btn-primary");

      var modal = bootstrap.Modal.getOrCreateInstance(modalEl, {
        backdrop: true,
        keyboard: true,
      });

      function finish() {
        resolve();
      }

      modalEl.addEventListener("hidden.bs.modal", finish, { once: true });
      btnOk.onclick = function () {
        modal.hide();
      };

      modal.show();
    });
  }

  global.patitasConfirmar = patitasConfirmar;
  global.patitasAlerta = patitasAlerta;
})(typeof window !== "undefined" ? window : global);
