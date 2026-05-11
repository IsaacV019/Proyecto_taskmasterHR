<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TaskMaster — Historial</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../css/fuentes.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="../index.php">
      <i class="bi bi-check2-square me-2" style="color:var(--accent2)"></i>Task<span class="brand-dot">Master</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link" href="../index.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="list.php"><i class="bi bi-list-task me-1"></i>Tareas</a></li>
        <li class="nav-item"><a class="nav-link active" href="history.php"><i class="bi bi-clock-history me-1"></i>Historial</a></li>

        <!-- Busqueda rapida global — mejorada -->
        <li class="nav-item ms-2">
          <div style="position:relative;display:flex;align-items:center">
            <i class="bi bi-search" style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--accent2);font-size:.82rem;pointer-events:none;z-index:1"></i>
            <input type="text" id="quickSearch" class="form-control form-control-sm"
              placeholder="Buscar tarea..." autocomplete="off"
              oninput="quickSearchFn(this.value)"
              onkeydown="if(event.key==='Escape'){this.value='';quickSearchFn('');}"
              style="padding-left:2.1rem;width:190px;font-size:.83rem;height:34px;border-radius:10px;background:rgba(99,106,248,.08);border:1.5px solid rgba(99,106,248,.35);color:var(--text);transition:width .25s ease">
            <div id="quickSearchResults" style="display:none;position:absolute;top:calc(100% + 8px);right:0;
              width:310px;background:var(--surface);border:1px solid var(--border2);
              border-radius:12px;box-shadow:var(--sh-lg);z-index:2000;max-height:340px;overflow-y:auto"></div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex">

  <aside class="sidebar d-none d-lg-block">
    <div class="sidebar-title">Navegación</div>
    <a href="../index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="list.php"     class="nav-link"><i class="bi bi-list-task"></i> Todas las tareas</a>
    <a href="history.php"  class="nav-link active"><i class="bi bi-clock-history"></i> Historial</a>
    <a href="form.php"     class="nav-link"><i class="bi bi-plus-circle"></i> Nueva tarea</a>

    <div class="sidebar-title">Filtrar por</div>
    <a href="#" class="nav-link" id="sbAll" onclick="setFilter('all');return false;">
      <i class="bi bi-list-ul"></i> Todo
      <span class="badge ms-auto" id="cntAll" style="background:var(--accent-bg);color:var(--accent2)">—</span>
    </a>
    <a href="#" class="nav-link" id="sbDeleted" onclick="setFilter('deleted');return false;">
      <i class="bi bi-trash" style="color:var(--red)"></i> Eliminadas
      <span class="badge ms-auto" id="cntDeleted" style="background:var(--red-bg);color:#ff6b63">—</span>
    </a>
    <a href="#" class="nav-link" id="sbCompleted" onclick="setFilter('completed');return false;">
      <i class="bi bi-check-circle" style="color:var(--green)"></i> Completadas
      <span class="badge ms-auto" id="cntCompleted" style="background:var(--green-bg);color:#34d65c">—</span>
    </a>

    <div class="sidebar-title">Opciones</div>
    <a href="#" class="nav-link" onclick="confirmClear();return false;" style="color:#ff6b63">
      <i class="bi bi-trash3"></i> Limpiar historial
    </a>
  </aside>

  <main class="main-content w-100">

    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h2><i class="bi bi-clock-history me-2" style="color:var(--accent2)"></i>Historial de Tareas</h2>
        <p>Registro de tareas eliminadas y completadas ·
          <span id="totalBadge" class="badge" style="background:var(--accent-bg);color:var(--accent2)">cargando...</span>
        </p>
      </div>
      <button class="btn btn-sm btn-outline-danger" onclick="confirmClear()">
        <i class="bi bi-trash3 me-1"></i>Limpiar todo
      </button>
    </div>

    <!-- Tarjetas resumen ----------------------------------------------------------------------------------->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-4 animate-in animate-in-1">
        <div class="stat-card stat-indigo">
          <span class="stat-icon">📋</span>
          <div class="stat-num" id="statTotal">—</div>
          <div class="stat-lbl">Total en historial</div>
        </div>
      </div>
      <div class="col-6 col-lg-4 animate-in animate-in-2">
        <div class="stat-card stat-red">
          <span class="stat-icon">🗑️</span>
          <div class="stat-num" id="statDeleted">—</div>
          <div class="stat-lbl">Eliminadas</div>
        </div>
      </div>
      <div class="col-6 col-lg-4 animate-in animate-in-3">
        <div class="stat-card stat-green">
          <span class="stat-icon">✅</span>
          <div class="stat-num" id="statCompleted">—</div>
          <div class="stat-lbl">Completadas</div>
        </div>
      </div>
    </div>

    <!-- Filtros ------------------------------------------------------------------------------------------------->
    <div class="filters-row d-flex flex-wrap gap-2 align-items-center animate-in mb-3">
      <div class="search-wrap flex-grow-1" style="min-width:180px;max-width:300px">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" class="form-control form-control-sm"
          placeholder="Buscar en historial..." oninput="onSearch(this.value)">
      </div>
      <select id="filterAction" class="form-select form-select-sm" style="width:auto" onchange="setFilter(this.value)">
        <option value="all">Todos los tipos</option>
        <option value="deleted">🗑️ Eliminadas</option>
        <option value="completed">✅ Completadas</option>
      </select>
      <select id="filterCategory" class="form-select form-select-sm" style="width:auto" onchange="loadHistory()">
        <option value="">Todas las categorías</option>
        <option value="Trabajo">Trabajo</option>
        <option value="Personal">Personal</option>
        <option value="Estudio">Estudio</option>
        <option value="Salud">Salud</option>
        <option value="General">General</option>
      </select>
      <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearFilters()" title="Limpiar filtros">
        <i class="bi bi-x-lg"></i>
      </button>
      <button class="btn btn-sm btn-outline-secondary" onclick="loadHistory()" title="Recargar">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>

    <!-- Lista historial --------------------------------------------------------------------------------------------->
    <div class="card animate-in animate-in-1">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-semibold">
          <i class="bi bi-clock-history me-2" style="color:var(--accent2)"></i>Eventos registrados
        </span>
        <span id="listCount" style="font-size:.82rem;color:var(--text3)">—</span>
      </div>
      <div id="historyList" style="min-height:200px">
        <div class="text-center py-5">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2 table-hint animate-in">
      <span><i class="bi bi-info-circle me-1"></i>Se registra automáticamente al eliminar o completar tareas</span>
      <span><kbd>Ctrl+N</kbd> nueva tarea</span>
    </div>
  </main>
</div>

<div class="modal fade" id="clearModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2" style="color:var(--red)"></i>Limpiar historial</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Borrar <strong>todo</strong> el historial?</p>
        <p class="text-muted" style="font-size:.85rem;margin:0">Las tareas activas no se ven afectadas.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger btn-sm" onclick="clearHistory()">
          <i class="bi bi-trash3 me-1"></i>Limpiar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="toast-container"></div>

<script src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/app.js"></script>
<script>
// para history.php ------------------------------------------------------------------------------->
API.base = '../api/';
let hFilters = { action: 'all', search: '', category: '' };
// Cargar y mostrar el historial completo
async function loadHistory() {
  const list = document.getElementById('historyList');
  list.innerHTML = '<div class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
  try {
    const p = new URLSearchParams();
    if (hFilters.action !== 'all') p.set('action',   hFilters.action);
    if (hFilters.search)           p.set('search',   hFilters.search);
    const cat = document.getElementById('filterCategory').value;
    if (cat)                       p.set('category', cat);
    p.set('limit', 200);

    const res  = await fetch(API.base + 'history.php?' + p.toString());
    const data = await res.json();
    if (!data.success) throw new Error(data.message);
    const items = data.data.items || [];
    const stats = data.data.stats || {};

    // Actualizar contadores----------------------------------------------------------------------------------
    setElText('statTotal',     stats.total     || 0);
    setElText('statDeleted',   stats.deleted   || 0);
    setElText('statCompleted', stats.completed || 0);
    document.getElementById('totalBadge').textContent  = (stats.total || 0) + ' registros';
    document.getElementById('cntAll').textContent      = stats.total     || 0;
    document.getElementById('cntDeleted').textContent  = stats.deleted   || 0;
    document.getElementById('cntCompleted').textContent= stats.completed || 0;
    document.getElementById('listCount').textContent   = items.length + ' evento' + (items.length!==1?'s':'');
    renderHistory(items);
  } catch(e) {
    list.innerHTML = `<div class="empty-state"><div class="icon">⚠️</div><h5>Error al cargar</h5><p>${e.message}</p></div>`;
  }
}
// Renderizar la lista de eventos agrupados por día
function renderHistory(items) {
  const list = document.getElementById('historyList');
  if (!items.length) {
    list.innerHTML = `<div class="empty-state">
      <div class="icon">🕐</div>
      <h5>Sin registros</h5>
      <p>Aquí aparecerán las tareas que elimines o marques como completadas.</p>
      <a href="list.php" class="btn btn-primary btn-sm mt-2"><i class="bi bi-list-task me-1"></i>Ver tareas</a>
    </div>`;
    return;
  }
  // Agrupar por fecha
  const groups = {};
  items.forEach(item => {
    const day = (item.happened_at || '').split(' ')[0] || 'Sin fecha';
    if (!groups[day]) groups[day] = [];
    groups[day].push(item);
  });
  let html = '';
  Object.entries(groups).forEach(([day, dayItems]) => {
    // Encabezado del día-----------------------------------------------------------------------------------------------
    html += `<div class="day-separator"><i class="bi bi-calendar3 me-1"></i>${dayLabel(day)}</div>`;
    dayItems.forEach((item, idx) => {
      const isDel  = item.action === 'deleted';
      const color  = isDel ? '#ff6b63' : '#34d65c';
      const bg     = isDel ? 'var(--red-bg)' : 'var(--green-bg)';
      const icon   = isDel ? 'bi-trash' : 'bi-check-circle';
      const label  = isDel ? 'Eliminada' : 'Completada';
      const border = isDel ? 'var(--red-bd)' : 'var(--green-bd)';
      const time   = (item.happened_at || '').split(' ')[1]?.substring(0,5) || '—';
      html += `<div class="history-item animate-in" style="animation-delay:${idx*0.03}s">
        <!-- Icono de la accion -->
        <div class="action-icon ${item.action}">
          <i class="bi ${icon}"></i>
        </div>
        <!-- Cuerpo del registro -->
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:3px">
            <span style="font-weight:600;color:var(--text);font-size:.93rem">${escHtml(item.task_title)}</span>
            <span class="badge-pill" style="background:${bg};color:${color};border:1px solid ${border};font-size:.65rem">${label}</span>
            <span class="badge-pill priority-${item.task_priority || 'media'}" style="font-size:.65rem">${capitalize(item.task_priority || 'media')}</span>
          </div>
          ${item.task_description ? `<div style="font-size:.8rem;color:var(--text3);margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:480px">${escHtml(item.task_description)}</div>` : ''}
          <div style="display:flex;gap:.6rem;flex-wrap:wrap;align-items:center">
            <span class="category-badge">${escHtml(item.task_category || 'General')}</span>
            ${item.task_due_date ? `<span style="font-size:.75rem;color:var(--text3)"><i class="bi bi-calendar2 me-1"></i>${formatDate(item.task_due_date)}</span>` : ''}
            <span style="font-size:.75rem;color:var(--text3)"><i class="bi bi-clock me-1"></i>${time}</span>
          </div>
        </div>
        <!-- Botón recuperar (solo para eliminadas) + ID -->
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.4rem;flex-shrink:0">
          ${isDel ? `<button onclick="restoreTask(${item.id}, '${escHtml(item.task_title).replace(/'/g,"\\'")}', this)"
            class="btn btn-sm btn-outline-success" style="font-size:.73rem;white-space:nowrap"
            title="Restaurar esta tarea como pendiente">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Recuperar
          </button>` : ''}
          <span class="row-num">#${item.id}</span>
        </div>
      </div>`;
    });
  });
  list.innerHTML = html;
}
// Etiqueta de día legible (Hoy / Ayer / fecha)----------------------------------------------------------------
function dayLabel(dateStr) {
  if (!dateStr || dateStr === 'Sin fecha') return 'Sin fecha';
  const diff = Math.floor((new Date() - new Date(dateStr + 'T00:00:00')) / 86400000);
  if (diff === 0) return 'Hoy';
  if (diff === 1) return 'Ayer';
  return new Date(dateStr + 'T00:00:00').toLocaleDateString('es-MX', {day:'numeric',month:'short',year:'numeric'});
}
// Filtros del sidebar y selects----------------------------------------------------------------------------------------------
function setFilter(action) {
  hFilters.action = action;
  document.getElementById('filterAction').value = action;
  // Marcar activo en el sidebar------------------------------------------------------------------------------------------
  ['sbAll','sbDeleted','sbCompleted'].forEach(id => document.getElementById(id)?.classList.remove('active'));
  const map = { all:'sbAll', deleted:'sbDeleted', completed:'sbCompleted' };
  if (map[action]) document.getElementById(map[action])?.classList.add('active');
  loadHistory();
}
let searchTimer;
function onSearch(val) {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => { hFilters.search = val; loadHistory(); }, 280);
}
function clearFilters() {
  hFilters = { action: 'all', search: '', category: '' };
  document.getElementById('searchInput').value    = '';
  document.getElementById('filterAction').value   = 'all';
  document.getElementById('filterCategory').value = '';
  loadHistory();
}
// Limpiar todo el historial -------------------------------------------------------------------------------------------
// nota: isaac termina esto ----------------------------------------------------------------------------------------------
function confirmClear() { new bootstrap.Modal(document.getElementById('clearModal')).show(); }
async function clearHistory() {
  try {
    const res = await fetch(API.base + 'history.php', { method: 'DELETE' });
    const data = await res.json();
    bootstrap.Modal.getInstance(document.getElementById('clearModal'))?.hide();
    Toast.show(data.success ? 'Historial limpiado' : 'Error al limpiar', data.success ? 'success' : 'danger');
    if (data.success) loadHistory();
  } catch(e) { Toast.show('Error de conexión','danger'); }
}

// Busqueda rapida ---------------------------------------------------------------------------------
// nota: esto se usa para la parte de actualizar y busca el historial de las tareas elminadas y completadas 
//  muy importante ---------------------------------------------------------------------------------------
let qsTimer;
async function quickSearchFn(val) {
  const drop = document.getElementById('quickSearchResults');
  if (!drop) return;
  if (!val || val.trim().length < 2) { drop.style.display='none'; drop.innerHTML=''; return; }
  clearTimeout(qsTimer);
  qsTimer = setTimeout(async () => {
    drop.style.display = 'block';
    drop.innerHTML = '<div style="padding:1rem;text-align:center"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    try {
      const res  = await fetch(API.base + 'tasks.php?search=' + encodeURIComponent(val.trim()) + '&limit=8');
      const data = await res.json();
      const tasks = data.data || [];
      if (!tasks.length) { drop.innerHTML = `<div style="padding:1.2rem;text-align:center;color:var(--text3);font-size:.85rem">Sin resultados</div>`; return; }
      drop.innerHTML = `<div style="padding:.4rem .9rem;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);border-bottom:1px solid var(--border)">${tasks.length} resultado${tasks.length!==1?'s':''}</div>`
        + tasks.map(t=>`<a href="detail.php?id=${t.id}" style="display:flex;align-items:center;gap:.7rem;padding:.6rem 1rem;border-bottom:1px solid var(--border);text-decoration:none;transition:var(--t)" onmouseover="this.style.background='rgba(99,106,248,.08)'" onmouseout="this.style.background='transparent'">
          <div style="width:28px;height:28px;border-radius:50%;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-file-text" style="font-size:.75rem;color:var(--accent2)"></i>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:.875rem;font-weight:600;color:var(--text)">${escHtml(t.title)}</div>
            <div style="font-size:.73rem;color:var(--text3)">${escHtml(t.category)}</div>
          </div>
          <span class="badge-pill status-${t.status.replace(' ','-')}" style="font-size:.62rem">${capitalize(t.status)}</span>
        </a>`).join('');
    } catch(e) { drop.innerHTML = '<div style="padding:1rem;color:var(--text3);font-size:.85rem">Error al buscar</div>'; }
  }, 220);
}
document.addEventListener('click', e => {
  if (!e.target.closest('#quickSearch') && !e.target.closest('#quickSearchResults'))
    document.getElementById('quickSearchResults').style.display='none';
});
document.addEventListener('DOMContentLoaded', () => {
  loadHistory();
  document.getElementById('sbAll').classList.add('active');
});
</script>
</body>
</html>
