/**
 * Panel administración — route=dashboard
 */
document.addEventListener('DOMContentLoaded', function () {
  var fa = document.getElementById('patitasFechaHoyAdmin');
  if (fa) {
    fa.textContent = new Date().toLocaleDateString('es-CR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric',
    });
  }

  if (typeof apiGetJson !== 'function') return;

  apiGetJson(patitasApi('dashboard')).then(function (data) {
    if (!data || !data.ok || data.vista !== 'staff') return;

    var s = data.stats || {};
    function set(id, val) {
      var el = document.getElementById(id);
      if (el) el.textContent = val != null ? String(val) : '0';
    }
    set('statUsuarios', s.usuarios);
    set('statAnimales', s.animales);
    set('statCitasHoy', s.citasHoy);
    set('statInventario', s.inventarioItems);
    set('statVets', s.veterinarios);
    var ing = s.ingresosMes != null ? '₡' + Number(s.ingresosMes).toLocaleString('es-CR') : '₡0';
    set('statIngresos', ing);

    var listaC = document.getElementById('listaCitasHoy');
    if (listaC && data.citasHoyList) {
      listaC.innerHTML = '';
      if (data.citasHoyList.length === 0) {
        listaC.innerHTML = '<div class="list-group-item text-muted small">Sin citas hoy</div>';
      } else {
        data.citasHoyList.forEach(function (c) {
          var hi = String(c.HORA_DE_INICIO || '').substring(0, 5);
          var div = document.createElement('div');
          div.className = 'list-group-item px-0 d-flex justify-content-between align-items-center';
          div.innerHTML =
            '<div><strong>' + hi + ' — ' + (c.mascota || '') + ' (' + (c.raza || '') + ')</strong><br>' +
            '<small class="text-muted">Dueño: ' + (c.dueno || c.DUENO || '') + ' · ' + (c.veterinario || '') + '</small></div>' +
            '<span class="badge bg-secondary rounded-pill">' + (c.estado || '') + '</span>';
          listaC.appendChild(div);
        });
      }
    }

    var listaU = document.getElementById('listaUsuariosRecientes');
    if (listaU && data.usuariosRecientes) {
      listaU.innerHTML = '';
        data.usuariosRecientes.forEach(function (u) {
        var div = document.createElement('div');
        div.className = 'list-group-item px-0 d-flex justify-content-between align-items-center';
        var em = u.email || u.EMAIL || '';
        div.innerHTML =
          '<div><strong>' + (u.nombre || '') + '</strong><br><small class="text-muted">' + em + '</small></div>' +
          '<span class="badge rounded-pill" style="background:#DCFCE7;color:#166534;">Cliente</span>';
        listaU.appendChild(div);
      });
    }
  }).catch(function () {});
});
