<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TaskMaster — Lista de Tareas</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../css/fuentes.css" rel="stylesheet">
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
        <!-- Búsqueda rápida global — mejorada -->
        <li class="nav-item ms-2">
          <div class="qs-wrap">
            <i class="bi bi-search qs-icon"></i>
            <input type="text" id="quickSearch" class="form-control form-control-sm"
              placeholder="Buscar tarea..." autocomplete="off" oninput="quickSearchFn(this.value)"
              onkeydown="if(event.key==='Escape'){this.value='';quickSearchFn('');}">
            <div id="quickSearchResults" style="display:none;position:absolute;top:calc(100% + 8px);right:0;
              width:310px;background:var(--surface);border:1px solid var(--border2);border-radius:12px;
              box-shadow:var(--sh-lg);z-index:2000;max-height:340px;overflow-y:auto"></div>
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
    <a href="list.php" class="nav-link active"><i class="bi bi-list-task"></i> Todas las tareas</a>
    <a href="form.php" class="nav-link"><i class="bi bi-plus-circle"></i> Nueva tarea</a>

    <div class="sidebar-title mt-3">Filtros rápidos</div>
    <a href="list.php" class="nav-link" onclick="clearFilters(); return false;">
      <i class="bi bi-x-circle"></i> Sin filtros
    </a>
    <a href="list.php?status=pendiente" class="nav-link" onclick="onFilterChange('status','pendiente'); return false;">
      <i class="bi bi-clock"></i> Pendientes
    </a>
    <a href="list.php?status=en+progreso" class="nav-link" onclick="onFilterChange('status','en progreso'); return false;">
      <i class="bi bi-arrow-repeat text-info"></i> En progreso
    </a>
    <a href="list.php?status=completada" class="nav-link" onclick="onFilterChange('status','completada'); return false;">
      <i class="bi bi-check-circle text-success"></i> Completadas
    </a>
    <a href="list.php?priority=alta" class="nav-link" onclick="onFilterChange('priority','alta'); return false;">
      <i class="bi bi-exclamation-circle text-danger"></i> Prioridad alta
    </a>
  </aside>

  <main class="main-content w-100">

    <div class="page-header d-flex justify-content-between align-items-start flex-wrap gap-2">
      <div>
        <h2><i class="bi bi-list-task me-2 text-primary"></i>Lista de Tareas</h2>
        <p>Gestiona, filtra y edita todas tus tareas · <span id="taskCount" class="badge bg-primary bg-opacity-25 text-primary">—</span></p>
      </div>
      <a href="form.php" class="btn btn-primary animate-in">
        <i class="bi bi-plus-lg"></i> Nueva Tarea
      </a>
    </div>

    <div class="filters-row d-flex flex-wrap gap-2 align-items-center animate-in">
      <!-- Search -->
      <div class="search-wrap flex-grow-1" style="min-width:200px; max-width:320px;">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" class="form-control form-control-sm"
          placeholder="Buscar tareas..."
          oninput="onSearchInput(this.value)">
      </div>

      <select id="filterPriority" class="form-select form-select-sm" style="width:auto;"
        onchange="onFilterChange('priority', this.value)">
        <option value="">Todas las prioridades</option>
        <option value="alta">🔴 Alta</option>
        <option value="media">🟡 Media</option>
        <option value="baja">🟢 Baja</option>
      </select>

      
      <select id="filterStatus" class="form-select form-select-sm" style="width:auto;"
        onchange="onFilterChange('status', this.value)">
        <option value="">Todos los estados</option>
        <option value="pendiente">⏳ Pendiente</option>
        <option value="en progreso">🔄 En progreso</option>
        <option value="completada">✅ Completada</option>
      </select>

      <select id="filterCategory" class="form-select form-select-sm" style="width:auto;"
        onchange="onFilterChange('category', this.value)">
        <option value="">Todas las categorías</option>
        <option value="Trabajo">Trabajo</option>
        <option value="Personal">Personal</option>
        <option value="Estudio">Estudio</option>
        <option value="Salud">Salud</option>
        <option value="General">General</option>
      </select>

      <button class="btn btn-sm btn-outline-secondary ms-auto" onclick="clearFilters()"
        data-bs-toggle="tooltip" title="Limpiar filtros">
        <i class="bi bi-x-lg"></i>
      </button>

      <button class="btn btn-sm btn-outline-secondary" onclick="loadTasks()"
        data-bs-toggle="tooltip" title="Recargar">
        <i class="bi bi-arrow-clockwise"></i>
      </button>
    </div>

    <!-- Tabla -->
    <div class="card animate-in animate-in-1">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th style="width:50px">#</th>
              <th>Tarea</th>
              <th>Prioridad</th>
              <th>Estado</th>
              <th>Categoría</th>
              <th>Vence</th>
              <th>Creada</th>
              <th style="width:120px">Acciones</th>
            </tr>
          </thead>
          <tbody id="tasksTableBody">
            <tr><td colspan="8" class="text-center py-4">
              <div class="spinner-border spinner-border-sm text-primary me-2"></div> Cargando...
            </td></tr>
          </tbody>
        </table>
      </div>
    </div>


    <div class="d-flex justify-content-between align-items-center mt-2 animate-in table-hint">
      <span><i class="bi bi-info-circle me-1"></i>Haz clic en una fila para ver el detalle completo</span>
      <span><kbd>Ctrl+N</kbd> para nueva tarea</span>
    </div>

  </main>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Eliminar tarea</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="mb-1">¿Eliminar: <strong id="confirmTaskTitle"></strong>?</p>
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

document.addEventListener('DOMContentLoaded', () => {
  const params = new URLSearchParams(window.location.search);
  if (params.get('status'))   { currentFilters.status   = params.get('status');   document.getElementById('filterStatus').value = params.get('status'); }
  if (params.get('priority')) { currentFilters.priority = params.get('priority'); document.getElementById('filterPriority').value = params.get('priority'); }
  if (params.get('category')) { currentFilters.category = params.get('category'); document.getElementById('filterCategory').value = params.get('category'); }
  loadTasks();
});
</script>
</body>
</html>
