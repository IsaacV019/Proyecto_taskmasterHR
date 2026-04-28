<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TaskMaster — Detalle de Tarea</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="../index.php">
      <i class="bi bi-check2-square me-2" style="color:#818cf8"></i>Task<span>Master</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link" href="../index.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/list.php"><i class="bi bi-list-task me-1"></i>Tareas</a></li>
        <li class="nav-item"><a class="nav-link" href="../pages/history.php"><i class="bi bi-clock-history me-1"></i>Historial</a></li>
        <!-- Búsqueda rápida global — Carlos Alberto Ridan Jardines -->
        <li class="nav-item ms-1">
          <div style="position:relative">
            <i class="bi bi-search" style="position:absolute;left:.7rem;top:50%;transform:translateY(-50%);color:var(--text3);font-size:.8rem;pointer-events:none;z-index:1"></i>
            <input type="text" id="quickSearch" class="form-control form-control-sm"
              placeholder="Buscar..." autocomplete="off" oninput="quickSearchFn(this.value)"
              onkeydown="if(event.key==='Escape'){this.value='';quickSearchFn('');}"
              style="padding-left:2rem;width:170px;font-size:.82rem">
            <div id="quickSearchResults" style="display:none;position:absolute;top:calc(100% + 6px);right:0;
              width:300px;background:var(--surface);border:1px solid var(--border2);border-radius:12px;
              box-shadow:var(--sh-lg);z-index:2000;max-height:340px;overflow-y:auto"></div>
          </div>
        </li>
        <li class="nav-item ms-1">
          <a href="../pages/form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Nueva tarea</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex">
  <aside class="sidebar d-none d-lg-block">
    <div class="sidebar-title">Navegación</div>
    <a href="../index.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="list.php" class="nav-link"><i class="bi bi-list-task"></i> Todas las tareas</a>
    <a href="form.php" class="nav-link"><i class="bi bi-plus-circle"></i> Nueva tarea</a>

    <div class="sidebar-title mt-3">Tarea actual</div>
    <div class="px-3">
      <div class="small text-muted" id="sideTaskTitle">Cargando...</div>
      <div class="mt-2" id="sideTaskStatus"></div>
      <div class="mt-1" id="sideTaskPriority"></div>
    </div>
  </aside>

  <main class="main-content w-100">

   
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="../index.php" class="text-decoration-none text-muted">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="list.php" class="text-decoration-none text-muted">Tareas</a></li>
        <li class="breadcrumb-item active text-light" id="bcTitle">Detalle</li>
      </ol>
    </nav>

    
    <div id="loadingState" class="text-center py-5">
      <div class="spinner-border text-primary mb-3"></div>
      <div class="text-muted">Cargando tarea...</div>
    </div>

   
    <div id="errorState" class="d-none">
      <div class="empty-state">
        <div class="icon">❌</div>
        <h5>Tarea no encontrada</h5>
        <p class="text-muted">La tarea que buscas no existe o fue eliminada.</p>
        <a href="list.php" class="btn btn-primary mt-2">Volver a la lista</a>
      </div>
    </div>

    
    <div id="detailContent" class="d-none animate-in">

      
      <div class="detail-header mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
          <div class="flex-grow-1">
            <div class="d-flex gap-2 flex-wrap mb-2" id="detailBadges"></div>
            <h2 class="fw-bold mb-1" id="detailTitle"></h2>
            <div class="table-hint" id="detailMeta"></div>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <a id="editBtn" href="#" class="btn btn-warning">
              <i class="bi bi-pencil me-1"></i>Editar
            </a>
            <button id="deleteBtn" class="btn btn-danger">
              <i class="bi bi-trash me-1"></i>Eliminar
            </button>
          </div>
        </div>
      </div>

      <div class="row g-3">
        
        <div class="col-lg-8">
          <div class="card h-100 animate-in animate-in-1">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-card-text me-2 text-primary"></i>Descripción</span>
            </div>
            <div class="card-body">
              <div id="detailDescription" class="text-muted"></div>
            </div>
          </div>
        </div>

       
        <div class="col-lg-4">
          <div class="card animate-in animate-in-2">
            <div class="card-header">
              <span class="fw-semibold"><i class="bi bi-info-circle me-2 text-primary"></i>Detalles</span>
            </div>
            <div class="card-body p-3">
              <div class="detail-field">
                <div class="label">Prioridad</div>
                <div class="value" id="detailPriority"></div>
              </div>
              <div class="detail-field">
                <div class="label">Estado</div>
                <div class="value" id="detailStatus"></div>
              </div>
              <div class="detail-field">
                <div class="label">Categoría</div>
                <div class="value" id="detailCategory"></div>
              </div>
              <div class="detail-field">
                <div class="label">Fecha límite</div>
                <div class="value" id="detailDueDate"></div>
              </div>
              <div class="detail-field">
                <div class="label">Creada</div>
                <div class="value" id="detailCreatedAt"></div>
              </div>
              <div class="detail-field mb-0">
                <div class="label">Última actualización</div>
                <div class="value" id="detailUpdatedAt"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="card mt-3 animate-in animate-in-3">
        <div class="card-header">
          <span class="fw-semibold"><i class="bi bi-arrow-repeat me-2 text-info"></i>Cambiar estado rápidamente</span>
        </div>
        <div class="card-body">
          <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-secondary btn-sm" onclick="changeStatus('pendiente')">
              <i class="bi bi-clock me-1"></i>Pendiente
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="changeStatus('en progreso')">
              <i class="bi bi-arrow-repeat me-1"></i>En progreso
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="changeStatus('completada')">
              <i class="bi bi-check-lg me-1"></i>Marcar completada
            </button>
          </div>
        </div>
      </div>

  
      <div class="d-flex justify-content-between mt-3 animate-in animate-in-4">
        <a href="list.php" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left me-1"></i> Volver a lista
        </a>
        <a href="form.php" class="btn btn-primary">
          <i class="bi bi-plus-lg me-1"></i> Nueva tarea
        </a>
      </div>
    </div>

  </main>
</div>


<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Eliminar</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Eliminar <strong id="confirmTaskTitle"></strong>?</p>
        <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-danger btn-sm" id="confirmDeleteBtn">
          <i class="bi bi-trash me-1"></i>Eliminar
        </button>
      </div>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/app.js"></script>
<script>
API.base = '../api/';

let currentTask = null;

async function loadDetail(id) {
  try {
    const res = await API.getTask(id);
    if (!res.success || !res.data) {
      document.getElementById('loadingState').classList.add('d-none');
      document.getElementById('errorState').classList.remove('d-none');
      return;
    }

    currentTask = res.data;
    const t     = currentTask;
    const today = new Date().toISOString().split('T')[0];

  
    document.getElementById('loadingState').classList.add('d-none');
    document.getElementById('detailContent').classList.remove('d-none');

    
    document.title = `TaskMaster — ${t.title}`;
    document.getElementById('bcTitle').textContent  = t.title;
    document.getElementById('detailTitle').textContent = t.title;
    document.getElementById('detailMeta').textContent =
      `Tarea #${t.id} · Creada el ${formatDate(t.created_at ? t.created_at.split(' ')[0] : '')}`;

    
    document.getElementById('sideTaskTitle').textContent = t.title;
    document.getElementById('sideTaskStatus').innerHTML   =
      `<span class="badge-pill status-${t.status.replace(' ','-')}">${capitalize(t.status)}</span>`;
    document.getElementById('sideTaskPriority').innerHTML =
      `<span class="badge-pill priority-${t.priority}">${capitalize(t.priority)}</span>`;

  
    document.getElementById('detailBadges').innerHTML =
      `<span class="badge-pill priority-${t.priority}">${capitalize(t.priority)}</span>
       <span class="badge-pill status-${t.status.replace(' ','-')}">${capitalize(t.status)}</span>
       <span class="badge bg-secondary bg-opacity-25 text-light">${t.category}</span>`;

    
    const descEl = document.getElementById('detailDescription');
    descEl.innerHTML = t.description
      ? `<p>${t.description.replace(/\n/g,'<br>')}</p>`
      : '<em class="text-muted">Sin descripción</em>';

   
    document.getElementById('detailPriority').innerHTML =
      `<span class="badge-pill priority-${t.priority}">${capitalize(t.priority)}</span>`;
    document.getElementById('detailStatus').innerHTML =
      `<span class="badge-pill status-${t.status.replace(' ','-')}">${capitalize(t.status)}</span>`;
    document.getElementById('detailCategory').textContent = t.category;

    const dueDateEl = document.getElementById('detailDueDate');
    if (t.due_date) {
      const overdue = t.due_date < today && t.status !== 'completada';
      const dueToday = t.due_date === today;
      dueDateEl.innerHTML = `
        <span class="${overdue?'text-danger':dueToday?'text-warning':''}">
          ${formatDate(t.due_date)}
          ${overdue ? ' <span class="badge bg-danger">Vencida</span>' : ''}
          ${dueToday ? ' <span class="badge bg-warning text-dark">Hoy</span>' : ''}
        </span>`;
    } else {
      dueDateEl.textContent = 'Sin fecha límite';
    }

    document.getElementById('detailCreatedAt').textContent  = t.created_at || '—';
    document.getElementById('detailUpdatedAt').textContent  = t.updated_at || '—';

    // Editar / Dorrar buttons
    document.getElementById('editBtn').href = `form.php?id=${t.id}`;
    document.getElementById('deleteBtn').onclick = () => {
      confirmDelete(t.id, t.title);
    };

   
    document.getElementById('confirmDeleteBtn').onclick = async () => {
      bootstrap.Modal.getInstance(document.getElementById('confirmModal')).hide();
      try {
        const r = await API.deleteTask(t.id);
        if (r.success) {
          Toast.show('Tarea eliminada', 'success');
          setTimeout(() => window.location.href = 'list.php', 1000);
        } else Toast.show(r.message || 'Error', 'danger');
      } catch(e) { Toast.show('Error de conexión', 'danger'); }
    };

  } catch(e) {
    document.getElementById('loadingState').classList.add('d-none');
    document.getElementById('errorState').classList.remove('d-none');
  }
}

async function changeStatus(newStatus) {
  if (!currentTask) return;
  try {
    const res = await API.updateTask(currentTask.id, { status: newStatus });
    if (res.success) {
      Toast.show(`Estado cambiado a: ${capitalize(newStatus)}`, 'success', 2000);
      loadDetail(currentTask.id);
    } else Toast.show(res.message || 'Error', 'danger');
  } catch(e) { Toast.show('Error de conexión', 'danger'); }
}

document.addEventListener('DOMContentLoaded', () => {
  const id = new URLSearchParams(window.location.search).get('id');
  if (id) loadDetail(parseInt(id));
  else {
    document.getElementById('loadingState').classList.add('d-none');
    document.getElementById('errorState').classList.remove('d-none');
  }
});
</script>
</body>
</html>
