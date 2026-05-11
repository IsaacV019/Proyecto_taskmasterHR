# TaskMaster

Aplicación web de gestión de tareas con CRUD completo, historial de actividad, búsqueda en tiempo real y dashboard con estadísticas.

---

## Información académica

| Campo       | Detalle                                              |
|-------------|------------------------------------------------------|
| Materia     | Programación Web                                     |
| NRC         | 111212 — Clave I5642 — CEDL-0209 — M-J 09:00–10:55  |
| Profesor    | Carlos Alberto Ridan Jardines                        |
| Integrantes | Jaime Isaac Velásquez Castañeda · Alex Javier Hernández Rosales |

---

## Descripción

TaskMaster es un gestor de tareas avanzado que permite organizar actividades con atributos como prioridad, estado, categoría y fecha límite. Incluye historial con recuperación de tareas eliminadas, búsqueda global y un dashboard visual con gráficas.

---

## Tecnologías

| Tecnología      | Uso                                              |
|-----------------|--------------------------------------------------|
| PHP             | API REST y lógica del backend                    |
| SQLite (PDO)    | Base de datos local (archivo)                    |
| JavaScript      | AJAX, validación y manipulación del DOM          |
| Bootstrap 5     | Layout responsive y componentes de UI            |
| Bootstrap Icons | Iconografía                                      |
| Chart.js        | Gráfica de dona en el dashboard                  |
| HTML5 / CSS3    | Estructura semántica y estilos personalizados    |
| Google Fonts    | Tipografía Inter                                 |

---

## Estructura del proyecto

```
taskmaster/
├── index.php              → Dashboard (página de inicio)
├── pages/
│   ├── list.php           → Listado de tareas con filtros
│   ├── form.php           → Crear y editar tareas
│   ├── detail.php         → Detalle de una tarea
│   └── history.php        → Historial de eliminadas y completadas
├── api/
│   ├── tasks.php          → CRUD de tareas (GET / POST / PUT / DELETE)
│   ├── history.php        → API de historial
│   ├── stats.php          → Estadísticas para el dashboard
│   └── categories.php     → Catálogo de categorías
├── db/
│   └── taskmaster.db      → Base de datos SQLite (generada automáticamente)
├── css/
│   └── style.css          → Estilos personalizados
├── js/
│   └── app.js             → JavaScript compartido por todas las páginas
└── README.md
```

---

## Instalación y ejecución

### Requisitos

- XAMPP con Apache y PHP 7.4 o superior
- Extensión SQLite habilitada (activa por defecto en XAMPP)
- Navegador moderno (Chrome, Firefox, Edge o Safari)

### Pasos

1. Copia la carpeta `taskmaster/` dentro de `htdocs`:
   ```
   C:\xampp\htdocs\Projects\taskmaster\
   ```
2. Inicia Apache desde el panel de XAMPP.
3. Abre el navegador en:
   ```
   http://localhost/Projects/taskmaster/
   ```
4. La base de datos se crea automáticamente al primer uso.

---

## API REST

### Tareas — `api/tasks.php`

| Método   | Ruta                  | Acción            |
|----------|-----------------------|-------------------|
| `GET`    | `api/tasks.php`       | Listar todas      |
| `GET`    | `api/tasks.php?id=N`  | Obtener una       |
| `POST`   | `api/tasks.php`       | Crear             |
| `PUT`    | `api/tasks.php?id=N`  | Actualizar        |
| `DELETE` | `api/tasks.php?id=N`  | Eliminar          |

### Historial — `api/history.php`

| Método   | Ruta              | Acción                  |
|----------|-------------------|-------------------------|
| `GET`    | `api/history.php` | Ver historial            |
| `POST`   | `api/history.php` | Registrar evento         |
| `PUT`    | `api/history.php` | Restaurar tarea eliminada|
| `DELETE` | `api/history.php` | Limpiar historial        |

---

## Funcionalidades

- CRUD completo sin recargar la página (AJAX)
- Historial automático de tareas eliminadas y completadas
- Recuperación de tareas eliminadas desde el historial
- Búsqueda global en tiempo real desde el navbar
- Filtros por estado, prioridad y categoría
- Validación de formularios con retroalimentación visual
- Modal de confirmación antes de eliminar
- Notificaciones toast por cada operación
- Dashboard con estadísticas, barra de progreso y gráfica de dona
- Sidebar con contadores de estado actualizados en tiempo real
- Indicadores de tareas vencidas o próximas a vencer
- Actualización automática de la lista cada 45 segundos
- Diseño oscuro responsive (móvil, tablet y escritorio)
