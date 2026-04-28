# TaskMaster

## Autor

Proyecto desarrollado como entrega final de **Programación Web
NRC 111212 | Clave I5642 | CEDL-0209 | M-J 0900:1055**.

- **Profesor:** Carlos Alberto Ridan Jardines
- **Equipo:**
  - Jaime Isaac Velásquez Castañeda
  - Alex Javier Hernández Rosales

## Descripción del Proyecto

**TaskMaster** es una aplicación web completa de gestión de tareas (to-do list avanzado) que permite crear, leer, actualizar y eliminar tareas con múltiples atributos como prioridad, estado, categoría y fecha límite. Incluye historial de actividad con recuperación de tareas eliminadas y búsqueda global en tiempo real.

## Estructura de carpetas

taskmaster/
├── index.php → Dashboard (página de inicio)
├── pages/
│ ├── list.php → Listado de tareas (con filtros)
│ ├── form.php → Crear / Editar tarea
│ ├── detail.php → Detalle de tarea
│ └── history.php → Historial de tareas eliminadas y completadas
├── api/
│ ├── tasks.php → API CRUD completa (GET/POST/PUT/DELETE)
│ ├── history.php → API de historial (GET/POST/PUT/DELETE)
│ ├── stats.php → Estadísticas para el dashboard
│ └── categories.php → Lista de categorías
├── db/
│ └── taskmaster.db → Base de datos SQLite
├── css/
│ └── style.css → Estilos personalizados
├── js/
│ └── app.js → JavaScript principal (compartido por todas las páginas)
└── README.md

## Instrucciones de ejecución

### Requisitos

- XAMPP (Apache + PHP 7.4+) o cualquier servidor PHP
- Extensión **SQLite** habilitada (incluida en XAMPP por defecto)
- Navegador moderno (Chrome, Firefox, Edge, Safari)

### Para ejecutar

1. Copiar la carpeta `taskmaster/` dentro de `htdocs/`:
   C:\xampp\htdocs\Projects\taskmaster\
2. Iniciar Apache desde el panel de XAMPP
3. Abrir en el navegador:
   http://localhost/Projects/taskmaster/
4. La base de datos SQLite **se crea automáticamente** si no existe (`db/taskmaster.db`).

## Tecnologías utilizadas

| Tecnología      | Uso                                        |
| --------------- | ------------------------------------------ |
| HTML5           | Estructura semántica de todas las páginas  |
| CSS3            | Estilos custom, variables CSS, animaciones |
| Bootstrap 5     | Layout responsive, componentes, utilidades |
| Bootstrap Icons | Iconografía                                |
| JavaScript      | Validación, DOM, AJAX, interactividad      |
| Chart.js        | Gráfica de dona en el dashboard            |
| PHP             | API REST, backend, conexión BD             |
| SQLite (PDO)    | Base de datos persistente (archivo local)  |
| Google Fonts    | Tipografía Inter                           |

## Funcionalidades CRUD — Tareas

| Operación       | Ruta                 | Método HTTP |
| --------------- | -------------------- | ----------- |
| **Crear** tarea | `api/tasks.php`      | `POST`      |
| **Leer** todas  | `api/tasks.php`      | `GET`       |
| **Leer** una    | `api/tasks.php?id=N` | `GET`       |
| **Actualizar**  | `api/tasks.php?id=N` | `PUT`       |
| **Eliminar**    | `api/tasks.php?id=N` | `DELETE`    |

## Funcionalidades CRUD — Historial

| Operación             | Ruta              | Método HTTP |
| --------------------- | ----------------- | ----------- |
| **Leer** historial    | `api/history.php` | `GET`       |
| **Registrar** evento  | `api/history.php` | `POST`      |
| **Restaurar** tarea   | `api/history.php` | `PUT`       |
| **Limpiar** historial | `api/history.php` | `DELETE`    |

## Características destacadas

- CRUD completo mediante API REST sin recargar la página
- Historial automático de tareas eliminadas y completadas
- **Recuperación de tareas eliminadas** desde el historial con un botón por registro
- **Búsqueda global en tiempo real** en el navbar (funciona en todas las páginas)
- Filtros en tiempo real por búsqueda, prioridad, estado y categoría
- Validación de formularios en tiempo real con retroalimentación visual
- Confirmación modal antes de eliminar registros
- Notificaciones tipo toast para cada operación
- Dashboard con estadísticas, barra de progreso y gráfica de dona (Chart.js)
- Sidebar con filtros rápidos y contadores de estado
- Indicadores visuales de tareas vencidas o próximas a vencer
- Cambio rápido de estado desde la vista de detalle
- Actualización automática de la lista cada 45 segundos
- Diseño en modo oscuro consistente
- Interfaz totalmente responsive (móvil, tablet, escritorio)
