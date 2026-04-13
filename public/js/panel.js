/**
 * Panel cliente — datos desde api/dashboard.php
 */
document.addEventListener('DOMContentLoaded', function () {
  var fk = document.getElementById('patitasFechaHoy');
  if (fk) {
    fk.textContent = new Date().toLocaleDateString('es-CR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  var bienvenida = document.getElementById('txtBienvenida');
  var raw = null;
  try {
    raw = localStorage.getItem('usuarioActivo');
  } catch (e) {}
  if (bienvenida && raw) {
    try {
      var u = JSON.parse(raw);
      if (u.nombre) {
        var primero = u.nombre.trim().split(/\s+/)[0] || u.nombre;
        bienvenida.textContent = '¡Hola, ' + primero + '!';
      }
    } catch (e2) {}
  }

  if (typeof apiGetJson !== 'function') return;

  apiGetJson('api/dashboard.php').then(function (data) {
    if (!data || !data.ok || data.vista !== 'cliente') return;

    var mascotas = data.mascotasPreview || [];
    var citas = data.citasProximas || [];
    var contM = document.getElementById('contenedor-mascotas-resumen');
    var contC = document.getElementById('contenedor-citas-proximas');

    if (contM) {
      contM.innerHTML = '';
      if (mascotas.length === 0) {
        contM.innerHTML =
          '<div class="col-12"><div class="patitas-empty-state border rounded-4 p-4 text-center bg-white shadow-sm">' +
          '<div class="mb-2 text-success" style="font-size:2rem;"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>' +
          '<p class="fw-semibold mb-1">Aún no tienes mascotas registradas</p>' +
          '<p class="text-muted small mb-0">Agrega tu primera mascota para agendar citas.</p></div></div>';
      } else {
        mascotas.forEach(function (m) {
          var col = document.createElement('div');
          col.className = 'col-12 col-md-6';
          col.innerHTML =
            '<div class="card border-0 shadow-sm">' +
            '<div class="card-body d-flex align-items-center gap-3">' +
            '<div class="pet-avatar"><i class="bi bi-heart-fill" aria-hidden="true"></i></div>' +
            '<div class="flex-grow-1">' +
            '<strong>' + (m.nombre || '') + '</strong><br>' +
            '<span class="text-muted">' + (m.especie || '') + ' — ' + (m.raza || '') + '</span>' +
            '</div>' +
            '<span class="text-muted small">' + (m.edad != null ? m.edad + ' años' : '') + '</span>' +
            '</div></div>';
          contM.appendChild(col);
        });
      }
    }

    if (contC) {
      contC.innerHTML = '';
      if (citas.length === 0) {
        contC.innerHTML =
          '<div class="col-12"><div class="patitas-empty-state border rounded-4 p-4 text-center bg-white shadow-sm">' +
          '<div class="mb-2 text-success" style="font-size:2rem;"><i class="bi bi-calendar-event" aria-hidden="true"></i></div>' +
          '<p class="fw-semibold mb-1">No tienes citas próximas</p>' +
          '<p class="text-muted small mb-0">Programa una consulta cuando la necesites.</p></div></div>';
      } else {
        citas.forEach(function (c) {
          var col = document.createElement('div');
          col.className = 'col-12 col-md-6';
          var hi = String(c.HORA_DE_INICIO || c.hora_inicio || '').substring(0, 5);
          var fd = c.FECHA || c.fecha || '';
          col.innerHTML =
            '<div class="card border-0 shadow-sm">' +
            '<div class="card-body d-flex align-items-center gap-3">' +
            '<div class="pet-avatar"><i class="bi bi-calendar-event" aria-hidden="true"></i></div>' +
            '<div class="flex-grow-1">' +
            '<strong>' + (c.mascota || '') + '</strong>' +
            '<p class="mb-0 small text-muted">' + hi + ' · ' + (c.veterinario || '') + '</p>' +
            '</div>' +
            '<span class="text-muted small">' + fd + '</span>' +
            '</div></div>';
          contC.appendChild(col);
        });
      }
    }
  }).catch(function () {});
});
