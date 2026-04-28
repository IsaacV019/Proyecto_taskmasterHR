
<!-- -Actualización automática de la lista cada 45 segundos para que esto no me lo muebas isaac
 es que dejalo asi ya depues lo termino 
 
 nota: "alertas, cambios visuales" los toasts y el modal de confirmación cubren eso perfectamente, tenlo presente para explicarlo.
  "actualización automática tras CRUD" también se cumple para lista se recarga sola después de cada operación y además tiene un auto-refresh cada 45 segundos.-->
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TaskMaster — Dashboard</title>
  <link href="css/estilos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="css/style.css" rel="stylesheet">

</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="index.php">
      <i class="bi bi-check2-square me-2" style="color:#818cf8"></i>Task<span>Master</span>
    </a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="pages/list.php"><i class="bi bi-list-task me-1"></i>Tareas</a></li>
        <li class="nav-item"><a class="nav-link" href="pages/history.php"><i class="bi bi-clock-history me-1"></i>Historial</a></li>
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
    <div class="sidebar-title">Principal</div>
    <a href="index.php" class="nav-link active">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="pages/list.php" class="nav-link">
      <i class="bi bi-list-task"></i> Todas las tareas
      <span class="badge bg-primary bg-opacity-25 text-primary" id="sidebarTotal">—</span>
    </a>
    <a href="pages/form.php" class="nav-link">
      <i class="bi bi-plus-circle"></i> Nueva tarea
    </a>

    <div class="sidebar-title mt-3">Por estado</div>
    <a href="pages/list.php?status=pendiente" class="nav-link">
      <i class="bi bi-clock"></i> Pendientes
      <span class="badge bg-secondary bg-opacity-25 text-secondary" id="sidebarPend">—</span>
    </a>
    <a href="pages/list.php?status=en+progreso" class="nav-link">
      <i class="bi bi-arrow-repeat text-info"></i> En progreso
      <span class="badge bg-info bg-opacity-25 text-info" id="sidebarProg">—</span>
    </a>
    <a href="pages/list.php?status=completada" class="nav-link">
      <i class="bi bi-check-circle text-success"></i> Completadas
      <span class="badge bg-success bg-opacity-25 text-success" id="sidebarComp">—</span>
    </a>

    <div class="sidebar-title mt-3">Por prioridad</div>
    <a href="pages/list.php?priority=alta" class="nav-link">
      <i class="bi bi-exclamation-circle text-danger"></i> Alta
    </a>
    <a href="pages/list.php?priority=media" class="nav-link">
      <i class="bi bi-dash-circle text-warning"></i> Media
    </a>
    <a href="pages/list.php?priority=baja" class="nav-link">
      <i class="bi bi-check-circle text-success"></i> Baja
    </a>

    <div class="sidebar-title mt-3">Accesos rápidos</div>
    <a href="pages/list.php" class="nav-link text-muted" style="font-size:0.8rem;">
      <i class="bi bi-keyboard"></i> Ctrl+N = nueva tarea
    </a>
  </aside>

 
  <main class="main-content w-100" id="dashboardWelcome">

    <!-- Page header -->
    <div class="page-header d-flex justify-content-between align-items-start">
      <div>
        <h2><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h2>
        <p>Resumen general de tus tareas · <span id="dateNow" class="text-muted"></span></p>
      </div>
      <a href="pages/form.php" class="btn btn-primary animate-in">
        <i class="bi bi-plus-lg"></i> Nueva Tarea
      </a>
    </div>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3 animate-in animate-in-1">
        <div class="stat-card stat-indigo">
          <span class="stat-icon">📋</span>
          <div class="stat-num" id="statTotal">—</div>
          <div class="stat-lbl">Total tareas</div>
        </div>
      </div>
      <div class="col-6 col-lg-3 animate-in animate-in-2">
        <div class="stat-card stat-yellow">
          <span class="stat-icon">⏳</span>
          <div class="stat-num" id="statPendiente">—</div>
          <div class="stat-lbl">Pendientes</div>
        </div>
      </div>
      <div class="col-6 col-lg-3 animate-in animate-in-3">
        <div class="stat-card stat-blue">
          <span class="stat-icon">🔄</span>
          <div class="stat-num" id="statProgreso">—</div>
          <div class="stat-lbl">En progreso</div>
        </div>
      </div>
      <div class="col-6 col-lg-3 animate-in animate-in-4">
        <div class="stat-card stat-green">
          <span class="stat-icon">✅</span>
          <div class="stat-num" id="statCompletada">—</div>
          <div class="stat-lbl">Completadas</div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
  
      <div class="col-lg-8 animate-in">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>Progreso General</span>
            <span class="table-hint" id="progressLabel">Cargando...</span>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between mb-1">
                <small class="text-muted">Tareas completadas</small>
                <small class="fw-semibold" id="progressPct">0%</small>
              </div>
              <div class="progress" style="height:12px">
                <div id="completionBar" class="progress-bar bg-success" role="progressbar" style="width:0%;transition:width 1s ease"></div>
              </div>
            </div>

            <!-- Recent tasks -->
            <div class="mt-3">
              <div class="fw-semibold mb-2 small text-muted text-uppercase" style="letter-spacing:.06em">Tareas recientes</div>
              <div class="table-responsive">
                <table class="table table-sm mb-0">
                  <thead>
                    <tr>
                      <th>Tarea</th><th>Prioridad</th><th>Estado</th><th>Vence</th>
                    </tr>
                  </thead>
                  <tbody id="recentTasksList">
                    <tr><td colspan="4" class="text-center text-muted py-3">
                      <div class="spinner-border spinner-border-sm"></div>
                    </td></tr>
                  </tbody>
                </table>
              </div>
              <a href="pages/list.php" class="btn btn-sm btn-outline-primary mt-2 w-100">
                Ver todas las tareas <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Doughnut chart -->
      <div class="col-lg-4 animate-in animate-in-1">
        <div class="card h-100">
          <div class="card-header">
            <span class="fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Por Prioridad</span>
          </div>
          <div class="card-body d-flex flex-column">
            <div class="chart-wrap flex-grow-1">
              <canvas id="priorityChart"></canvas>
            </div>
            <div id="categoryBreakdown" class="mt-3"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick actions -->
    <div class="row g-3 animate-in">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <span class="fw-semibold"><i class="bi bi-lightning me-2 text-warning"></i>Acciones rápidas</span>
          </div>
          <div class="card-body">
            <div class="row g-2">
              <div class="col-6 col-md-3">
                <a href="pages/form.php" class="btn btn-primary w-100">
                  <i class="bi bi-plus-lg me-2"></i>Nueva Tarea
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="pages/list.php?status=pendiente" class="btn btn-outline-warning w-100">
                  <i class="bi bi-clock me-2"></i>Ver Pendientes
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="pages/list.php?priority=alta" class="btn btn-outline-danger w-100">
                  <i class="bi bi-exclamation-circle me-2"></i>Prioridad Alta
                </a>
              </div>
              <div class="col-6 col-md-3">
                <a href="pages/list.php?status=completada" class="btn btn-outline-success w-100">
                  <i class="bi bi-check-circle me-2"></i>Completadas
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

<!-- ══ CONFIRM MODAL ══════════════════════════════ -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">
          <i class="bi bi-exclamation-triangle text-danger me-2"></i>Confirmar eliminación
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Eliminar la tarea <strong id="confirmTaskTitle"></strong>?</p>
        <p class="text-muted small mb-0">Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">
          <i class="bi bi-trash me-1"></i>Eliminar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container position-fixed top-0 end-0 p-3"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="js/app.js"></script>
<script>
// Dashboard extras
document.getElementById('dateNow').textContent = new Date().toLocaleDateString('es-MX',{weekday:'long',year:'numeric',month:'long',day:'numeric'});

// Override loadStats to also update sidebar + chart
const _origLoadStats = loadStats;
async function loadStats() {
  try {
    const res = await API.getStats();
    if (!res.success) return;
    const s = res.data;

    // Stat cards
    setElText('statTotal',      s.total);
    setElText('statPendiente',  s.pendiente);
    setElText('statProgreso',   s.en_progreso);
    setElText('statCompletada', s.completada);

    // Sidebar
    setElText('sidebarTotal', s.total);
    setElText('sidebarPend',  s.pendiente);
    setElText('sidebarProg',  s.en_progreso);
    setElText('sidebarComp',  s.completada);

    // Progress bar
    const pct = s.total > 0 ? Math.round((s.completada / s.total) * 100) : 0;
    document.getElementById('completionBar').style.width = pct + '%';
    document.getElementById('progressPct').textContent = pct + '%';
    document.getElementById('progressLabel').textContent =
      `${s.completada} de ${s.total} completadas`;

    // Chart
    if (!window.taskChart) initChart(s.by_priority);
    else updateChart(s.by_priority);

    // Category breakdown
    if (s.by_category) {
      document.getElementById('categoryBreakdown').innerHTML = s.by_category.map(c =>
        `<div class="d-flex justify-content-between align-items-center mb-1">
          <span class="small text-muted">${c.category}</span>
          <span class="badge bg-secondary bg-opacity-25 text-light">${c.cnt}</span>
        </div>`
      ).join('');
    }

    // Recent tasks
    if (s.recent) renderRecentTasks(s.recent);

  } catch(e) { console.error(e); }
}
loadStats();
</script>
</body>
</html>
