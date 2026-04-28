<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title id="pageTitle">TaskMaster — Nueva Tarea</title>
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
    <a href="form.php" class="nav-link active"><i class="bi bi-plus-circle"></i> Nueva tarea</a>

    <div class="sidebar-title mt-3">Ayuda</div>
    <div class="px-3 py-2">
      <small class="text-muted">
        <i class="bi bi-info-circle me-1"></i>
        Llena todos los campos marcados con <span class="text-danger">*</span> para crear la tarea.
      </small>
    </div>
    <div class="px-3 mt-2">
      <small class="text-muted">
        <i class="bi bi-keyboard me-1"></i>
        Los campos se validan en tiempo real.
      </small>
    </div>
  </aside>


  <main class="main-content w-100">

    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h2 id="formHeading"><i class="bi bi-plus-circle me-2 text-primary"></i>Nueva Tarea</h2>
        <p id="formSubheading">Completa el formulario para agregar una nueva tarea</p>
      </div>
      <a href="list.php" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Volver
      </a>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-7">
        <div class="card animate-in">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold" id="cardTitle">
              <i class="bi bi-pencil-square me-2 text-primary"></i>Datos de la tarea
            </span>
            <span class="table-hint">Los campos con <span class="text-danger">*</span> son obligatorios</span>
          </div>
          <div class="card-body p-4">

            <form id="taskForm" novalidate>

              
              <div class="mb-3">
                <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control"
                  placeholder="Ej: Preparar informe del proyecto"
                  maxlength="100" data-validate required>
                <div class="d-flex justify-content-between">
                  <div class="invalid-feedback"></div>
                  <small class="text-muted ms-auto" id="titleCounter">0/100</small>
                </div>
              </div>

             
              <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea name="description" id="description" class="form-control"
                  rows="3" maxlength="500" data-validate
                  placeholder="Descripción detallada de la tarea (opcional)..."></textarea>
                <div class="invalid-feedback"></div>
              </div>

             
              <div class="row g-3 mb-3">
                <div class="col-sm-6">
                  <label for="priority" class="form-label">Prioridad <span class="text-danger">*</span></label>
                  <select name="priority" id="priority" class="form-select" data-validate required>
                    <option value="">— Seleccionar —</option>
                    <option value="alta">🔴 Alta</option>
                    <option value="media" selected>🟡 Media</option>
                    <option value="baja">🟢 Baja</option>
                  </select>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-sm-6">
                  <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                  <select name="status" id="status" class="form-select" data-validate required>
                    <option value="">— Seleccionar —</option>
                    <option value="pendiente" selected>⏳ Pendiente</option>
                    <option value="en progreso">🔄 En progreso</option>
                    <option value="completada">✅ Completada</option>
                  </select>
                  <div class="invalid-feedback"></div>
                </div>
              </div>

              <div class="row g-3 mb-4">
                <div class="col-sm-6">
                  <label for="category" class="form-label">Categoría <span class="text-danger">*</span></label>
                  <select name="category" id="category" class="form-select" data-validate required>
                    <option value="">— Seleccionar —</option>
                    <option value="Trabajo">💼 Trabajo</option>
                    <option value="Personal">👤 Personal</option>
                    <option value="Estudio">📚 Estudio</option>
                    <option value="Salud">🏥 Salud</option>
                    <option value="General" selected>📋 General</option>
                  </select>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="col-sm-6">
                  <label for="due_date" class="form-label">Fecha límite</label>
                  <input type="date" name="due_date" id="due_date" class="form-control" data-validate>
                  <div class="invalid-feedback"></div>
                </div>
              </div>

             
              <div class="mb-4 p-3 rounded" style="background:rgba(79,70,229,0.08);border:1px solid rgba(79,70,229,0.2);" id="previewBox">
                <div class="small fw-semibold text-muted mb-2 text-uppercase" style="letter-spacing:.06em;">Vista previa</div>
                <div class="d-flex gap-2 flex-wrap align-items-center">
                  <span id="prevTitle" class="fw-semibold text-light">—</span>
                  <span id="prevPriority" class="badge-pill priority-media">Media</span>
                  <span id="prevStatus" class="badge-pill status-pendiente">Pendiente</span>
                  <span id="prevCategory" class="badge bg-secondary bg-opacity-25 text-light">General</span>
                </div>
              </div>

              <!-- Buttons -->
              <div class="d-flex gap-2 justify-content-end">
                <a href="list.php" class="btn btn-secondary">
                  <i class="bi bi-x-lg me-1"></i>Cancelar
                </a>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Limpiar
                </button>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                  <i class="bi bi-plus-lg me-1"></i>Crear tarea
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>


<div class="toast-container position-fixed top-0 end-0 p-3"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/app.js"></script>
<script>
API.base = '../api/';

let isEdit = false;
let editId = null;


function updatePreview() {
  const title    = document.getElementById('title').value.trim() || '—';
  const priority = document.getElementById('priority').value || 'media';
  const status   = document.getElementById('status').value || 'pendiente';
  const cat      = document.getElementById('category').value || 'General';

  document.getElementById('prevTitle').textContent    = title;
  document.getElementById('prevPriority').className   = 'badge-pill priority-' + priority;
  document.getElementById('prevPriority').textContent = priority.charAt(0).toUpperCase()+priority.slice(1);
  document.getElementById('prevStatus').className     = 'badge-pill status-' + status.replace(' ','-');
  document.getElementById('prevStatus').textContent   = status.charAt(0).toUpperCase()+status.slice(1);
  document.getElementById('prevCategory').textContent = cat;
}

['title','priority','status','category'].forEach(id => {
  document.getElementById(id)?.addEventListener('input', updatePreview);
  document.getElementById(id)?.addEventListener('change', updatePreview);
});


document.getElementById('taskForm').addEventListener('submit', async e => {
  e.preventDefault();
  await submitTaskForm(e.target, isEdit, editId);
});
async function loadForEdit(id) {
  try {
    const res = await API.getTask(id);
    if (!res.success) { Toast.show('Tarea no encontrada', 'danger'); return; }
    const t = res.data;

    document.getElementById('title').value       = t.title;
    document.getElementById('description').value = t.description;
    document.getElementById('priority').value    = t.priority;
    document.getElementById('status').value      = t.status;
    document.getElementById('category').value    = t.category;
    document.getElementById('due_date').value    = t.due_date;
    document.getElementById('titleCounter').textContent = `${t.title.length}/100`;

    updatePreview();

    // Update UI
    document.title = 'TaskMaster — Editar Tarea';
    document.getElementById('pageTitle').textContent = 'TaskMaster — Editar Tarea';
    document.getElementById('formHeading').innerHTML =
      '<i class="bi bi-pencil me-2 text-warning"></i>Editar Tarea';
    document.getElementById('formSubheading').textContent = `Editando: ${t.title}`;
    document.getElementById('cardTitle').innerHTML =
      '<i class="bi bi-pencil-square me-2 text-warning"></i>Modificar datos';
    document.getElementById('submitBtn').innerHTML =
      '<i class="bi bi-check-lg me-1"></i>Guardar cambios';
    document.getElementById('submitBtn').className = 'btn btn-warning';

  } catch(e) { Toast.show('Error al cargar la tarea', 'danger'); }
}

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  const id = params.get('id');
  if (id) { isEdit = true; editId = parseInt(id); loadForEdit(editId); }
  updatePreview();

  
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('due_date').min = today;
});
</script>
</body>
</html>
