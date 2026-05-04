<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title id="pageTitle">TaskMaster — Nueva Tarea</title>
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="../css/fuentes.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid px-3">
    <a class="navbar-brand" href="../index.php">
      <i class="bi bi-check2-square me-2"></i>Task<span>Master</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <!-- <button> → Botón para menú responsive -->
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navMain">
      <!-- collapse → Hace que el menú se oculte en móviles -->
      <ul class="navbar-nav ms-auto">
        <!-- <ul> → Lista de navegación -->
        <!-- <li> → Elementos de la lista -->
        <li class="nav-item">
          <a class="nav-link" href="../index.php">Dashboard</a>
        </li>
        <!-- Input de búsqueda -->
        <li class="nav-item">
          <input type="text" id="quickSearch" class="form-control form-control-sm"
            placeholder="Buscar..." oninput="quickSearchFn(this.value)">
          <!-- <input> → Campo de texto -->
          <!-- oninput → Ejecuta función JS al escribir -->
        </li>
        <li class="nav-item">
          <a href="../pages/form.php" class="btn btn-primary btn-sm">Nueva tarea</a>
          <!-- class="btn" → Estilo de botón con Bootstrap -->
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- Contenedor principal ------------------------------------------------------------------------>
<div class="d-flex">
  <!-- Sidebar (menú lateral) -->
  <aside class="sidebar d-none d-lg-block">
    <a href="../index.php" class="nav-link">Dashboard</a>
    <a href="list.php" class="nav-link">Todas las tareas</a>
    <a href="form.php" class="nav-link active">Nueva tarea</a>
  </aside>

  <!-- Contenido principal ---------------------------------------------------------------------->
  <main class="main-content w-100">
    <h2>Nueva Tarea</h2>
    <form id="taskForm" novalidate>
      <!-- <form> → Contenedor de formulario -->
      <!-- novalidae → Desactiva validación automática del navegador -->
      <div class="mb-3">
        <label for="title">Título *</label>
        <!-- <label> → Etiqueta del campo -->
        <input type="text" id="title" class="form-control" required>
        <!-- required → Campo obligatorio -->
      </div>
      <div class="mb-3">
        <label for="description">Descripción</label>
        <textarea id="description" class="form-control"></textarea>
        <!-- <textarea> → Campo de texto largo -->
      </div>

      <div class="mb-3">
        <label for="priority">Prioridad</label>
        <select id="priority" class="form-select">
          <!-- <select> → Lista desplegable -->
          <option value="alta">Alta</option>
          <!-- <option> → Opción dentro del select -->
        </select>
      </div>

      <div class="mb-3">
        <label for="due_date">Fecha límite</label>
        <input type="date" id="due_date" class="form-control">
        <!-- type="date" → Selector de fecha -->
      </div>

      <!-- Botones -->
      <button type="reset" class="btn btn-outline-secondary">Limpiar</button>
      <!-- reset → Limpia el formulario -->

      <button type="submit" class="btn btn-primary">Crear tarea</button>
      <!-- submit → Envía el formulario -->
    </form>
  </main>
</div>

<!-- Scripts -->
<script src="../js/bootstrap.bundle.min.js"></script>
<!-- <script> → Importa archivos JavaScript -->
<script src="../js/app.js"></script>
<script>
// JavaScript → Lógica de la página para que no se me olvide siempre se debe iniciar para hacer funcionar el js
// Escucha cuando el formulario se envía
document.getElementById('taskForm').addEventListener('submit', async e => {
  e.preventDefault(); // Evita que se recargue la página
  await submitTaskForm(e.target); // Envía los datos con JS
});
// Se ejecuta cuando carga la página----------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', () => {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('due_date').min = today;
  // Define la fecha mínima como hoy--------------------------------------------------------------------------------
});
</script>
</body>
</html>