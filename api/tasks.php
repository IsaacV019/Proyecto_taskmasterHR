<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

function getDB() {
    $dbPath = __DIR__ . '/../db/taskmaster.db';
    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
        return $pdo;
    } catch (PDOException $e) {
        jsonError('No se pudo conectar a la base de datos: ' . $e->getMessage(), 500);
    }
}

function jsonSuccess($data, $message = 'OK', $code = 200) {
    http_response_code($code);
    echo json_encode(['success' => true, 'message' => $message, 'data' => $data]);
    exit;
}
function jsonError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message, 'data' => null]);
    exit;
}
function sanitize($str) {
    return htmlspecialchars(trim((string)$str), ENT_QUOTES, 'UTF-8');
}
function getInput() {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function validateTask($data) {
    $errors = [];
    if (empty($data['title']))                          $errors[] = 'El título es obligatorio.';
    if (!empty($data['title']) && strlen($data['title']) < 3) $errors[] = 'El título debe tener al menos 3 caracteres.';
    if (!empty($data['title']) && strlen($data['title']) > 100) $errors[] = 'El título no puede superar 100 caracteres.';
    $validPriorities = ['alta','media','baja'];
    $validStatuses   = ['pendiente','en progreso','completada'];
    if (!empty($data['priority']) && !in_array($data['priority'], $validPriorities)) $errors[] = 'Prioridad inválida.';
    if (!empty($data['status'])   && !in_array($data['status'], $validStatuses))     $errors[] = 'Estado inválido.';
    if (!empty($data['due_date']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['due_date'])) $errors[] = 'Fecha inválida.';
    return $errors;
}

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$db     = getDB();

switch ($method) {

  
    case 'GET':
        if ($id) {
         
            $stmt = $db->prepare('SELECT * FROM tasks WHERE id = ?');
            $stmt->execute([$id]);
            $task = $stmt->fetch();
            if (!$task) jsonError('Tarea no encontrada', 404);
            jsonSuccess($task);
        } else {
            
            $where  = [];
            $params = [];

            if (!empty($_GET['search'])) {
                $where[] = "(title LIKE ? OR description LIKE ? OR category LIKE ?)";
                $s = '%' . $_GET['search'] . '%';
                $params = array_merge($params, [$s, $s, $s]);
            }
            if (!empty($_GET['priority'])) {
                $where[] = 'priority = ?';
                $params[] = $_GET['priority'];
            }
            if (!empty($_GET['status'])) {
                $where[] = 'status = ?';
                $params[] = $_GET['status'];
            }
            if (!empty($_GET['category'])) {
                $where[] = 'category = ?';
                $params[] = $_GET['category'];
            }

            // row_num = numero de orden visual secuencial (se recalcula al borrar tareas)
            // El usuario ve #1, #2, #3 siempre sin huecos, aunque se borren tareas
            $whereClause = $where ? ' WHERE ' . implode(' AND ', $where) : '';
            $orderBy = 'CASE priority WHEN "alta" THEN 1 WHEN "media" THEN 2 ELSE 3 END, CASE status WHEN "pendiente" THEN 1 WHEN "en progreso" THEN 2 ELSE 3 END, due_date ASC, id DESC';
            $sqlInner = 'SELECT *, ROW_NUMBER() OVER (ORDER BY ' . $orderBy . ') AS row_num FROM tasks';
            $sql = 'SELECT * FROM (' . $sqlInner . ') AS ranked' . $whereClause . ' ORDER BY row_num ASC';
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $tasks = $stmt->fetchAll();

            jsonSuccess($tasks, 'OK');
        }
        break;


    case 'POST':
        $data   = getInput();
        $errors = validateTask($data);
        if ($errors) jsonError(implode(' ', $errors));

        $stmt = $db->prepare('
            INSERT INTO tasks (title, description, priority, status, category, due_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            sanitize($data['title']),
            sanitize($data['description'] ?? ''),
            $data['priority'] ?? 'media',
            $data['status']   ?? 'pendiente',
            sanitize($data['category'] ?? 'General'),
            $data['due_date'] ?? '',
        ]);
        $newId = $db->lastInsertId();
        $stmt  = $db->prepare('SELECT * FROM tasks WHERE id = ?');
        $stmt->execute([$newId]);
        $task  = $stmt->fetch();
        jsonSuccess($task, 'Tarea creada correctamente', 201);
        break;

    case 'PUT':
        if (!$id) jsonError('ID requerido para actualizar');
        $data = getInput();

        // Check exists
        $exists = $db->prepare('SELECT id FROM tasks WHERE id = ?');
        $exists->execute([$id]);
        if (!$exists->fetch()) jsonError('Tarea no encontrada', 404);

        $current = $db->prepare('SELECT * FROM tasks WHERE id = ?');
        $current->execute([$id]);
        $errors = validateTask(array_merge($current->fetch(), $data));
        if ($errors) jsonError(implode(' ', $errors));

      
        $allowed = ['title','description','priority','status','category','due_date'];
        $sets = []; $params = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $sets[]   = "$field = ?";
                $params[] = in_array($field, ['title','description','category'])
                    ? sanitize($data[$field])
                    : $data[$field];
            }
        }
        if (empty($sets)) jsonError('No hay campos para actualizar');

        $sets[]   = "updated_at = datetime('now','localtime')";
        $params[] = $id;
        $sql = 'UPDATE tasks SET ' . implode(', ', $sets) . ' WHERE id = ?';
        $db->prepare($sql)->execute($params);

        $stmt2 = $db->prepare('SELECT * FROM tasks WHERE id = ?'); $stmt2->execute([$id]); $task = $stmt2->fetch();

        // Si cambio el estado a completada, guardarlo en el historial
        if (isset($data['status']) && $data['status'] === 'completada') {
            $db->exec("CREATE TABLE IF NOT EXISTS history (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                task_id INTEGER, task_title TEXT NOT NULL,
                task_description TEXT DEFAULT '', task_priority TEXT DEFAULT 'media',
                task_category TEXT DEFAULT 'General', task_due_date TEXT DEFAULT '',
                action TEXT NOT NULL, action_detail TEXT DEFAULT '', happened_at TEXT NOT NULL
            )");
            // Verificar que no este duplicado en el historial
            $dup = $db->prepare("SELECT id FROM history WHERE task_id=? AND action='completed' LIMIT 1");
            $dup->execute([$id]);
            if (!$dup->fetch()) {
                $h = $db->prepare("INSERT INTO history (task_id,task_title,task_description,task_priority,task_category,task_due_date,action,action_detail,happened_at)
                    VALUES (?,?,?,?,?,?,'completed','Tarea marcada como completada',datetime('now','localtime'))");
                $h->execute([$task['id'],$task['title'],$task['description'],$task['priority'],$task['category'],$task['due_date']]);
            }
        }

        jsonSuccess($task, 'Tarea actualizada correctamente');
        break;

    case 'DELETE':
        if (!$id) jsonError('ID requerido para eliminar');

        // Traer todos los datos ANTES de borrar para guardarlos en el historial
        $check = $db->prepare('SELECT * FROM tasks WHERE id = ?');
        $check->execute([$id]);
        $task = $check->fetch();
        if (!$task) jsonError('Tarea no encontrada', 404);

        // Borrar la tarea de la tabla principal
        $db->prepare('DELETE FROM tasks WHERE id = ?')->execute([$id]);

        // Registrar en historial automaticamente al eliminar
        // Asi el usuario puede ver que tareas borro y cuando
        $db->exec("CREATE TABLE IF NOT EXISTS history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            task_id INTEGER, task_title TEXT NOT NULL,
            task_description TEXT DEFAULT '', task_priority TEXT DEFAULT 'media',
            task_category TEXT DEFAULT 'General', task_due_date TEXT DEFAULT '',
            action TEXT NOT NULL, action_detail TEXT DEFAULT '', happened_at TEXT NOT NULL
        )");
        $h = $db->prepare("INSERT INTO history (task_id,task_title,task_description,task_priority,task_category,task_due_date,action,action_detail,happened_at)
            VALUES (?,?,?,?,?,?,'deleted','Tarea eliminada por el usuario',datetime('now','localtime'))");
        $h->execute([$task['id'],$task['title'],$task['description'],$task['priority'],$task['category'],$task['due_date']]);

        jsonSuccess(['id' => $id, 'title' => $task['title']], 'Tarea eliminada correctamente');
        break;

    default:
        jsonError('Método no permitido', 405);
}
?>
