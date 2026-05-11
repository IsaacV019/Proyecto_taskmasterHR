<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

function getDB() {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Crear la tabla si es la primera vez que se llama 
    $pdo->exec("CREATE TABLE IF NOT EXISTS history (
        id              INTEGER PRIMARY KEY AUTOINCREMENT,
        task_id         INTEGER,
        task_title      TEXT    NOT NULL,
        task_description TEXT   DEFAULT '',
        task_priority   TEXT    DEFAULT 'media',
        task_category   TEXT    DEFAULT 'General',
        task_due_date   TEXT    DEFAULT '',
        action          TEXT    NOT NULL,
        action_detail   TEXT    DEFAULT '',
        happened_at     TEXT    NOT NULL
    )");
    return $pdo;
}

function ok($data, $msg = 'OK')  { echo json_encode(['success'=>true,  'message'=>$msg, 'data'=>$data]); exit; }
function fail($msg, $code = 400) { http_response_code($code); echo json_encode(['success'=>false,'message'=>$msg,'data'=>null]); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$db     = getDB();

if ($method === 'GET') {
    $where  = [];
    $params = [];

    // Filtro por tipo: deleted / completed / all-----------------------------------------------------------
    if (!empty($_GET['action']) && $_GET['action'] !== 'all') {
        $where[]  = 'action = ?';
        $params[] = $_GET['action'];
    }
    // Busqueda por nombre de tarea esto hay que terminarlo-----------------------------------------------------------
    if (!empty($_GET['search'])) {
        $where[]  = 'task_title LIKE ?';
        $params[] = '%' . $_GET['search'] . '%';
    }
    // Filtro por categoria--------------------------------------------------------------------------------------------
    if (!empty($_GET['category'])) {
        $where[]  = 'task_category = ?';
        $params[] = $_GET['category'];
    }

    $limit    = min((int)($_GET['limit'] ?? 100), 300);
    $sql      = 'SELECT * FROM history'
              . ($where ? ' WHERE ' . implode(' AND ', $where) : '')
              . ' ORDER BY happened_at DESC LIMIT ?';
    $params[] = $limit;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // Contadores para las tarjetas de resumen-------------------------------------------------------------------------------------------
    $stats = $db->query("
        SELECT COUNT(*) AS total,
               SUM(action='deleted')   AS deleted,
               SUM(action='completed') AS completed
        FROM history
    ")->fetch();

    ok(['items' => $rows, 'stats' => $stats]);
}


if ($method === 'POST') {
    $data  = json_decode(file_get_contents('php://input'), true) ?? [];
    $title  = trim($data['task_title'] ?? '');
    $action = trim($data['action']     ?? '');
    if (!$title || !$action) fail('Faltan campos obligatorios');

    $now = date('Y-m-d H:i:s');
    $stmt = $db->prepare("INSERT INTO history
        (task_id,task_title,task_description,task_priority,task_category,task_due_date,action,action_detail,happened_at)
        VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $data['task_id']          ?? null,
        $title,
        trim($data['task_description'] ?? ''),
        $data['task_priority']    ?? 'media',
        trim($data['task_category'] ?? 'General'),
        $data['task_due_date']    ?? '',
        $action,
        trim($data['action_detail'] ?? ''),
        $now,
    ]);
    ok(['id' => $db->lastInsertId(), 'happened_at' => $now], 'Registrado');
}

if ($method === 'PUT') {
    $data  = json_decode(file_get_contents('php://input'), true) ?? [];
    $histId = (int)($data['history_id'] ?? 0);
    if (!$histId) fail('ID de historial requerido');

    // Buscar el registro en el historial-------------------------------------------------------------------------------------------
    $stmt = $db->prepare('SELECT * FROM history WHERE id = ?');
    $stmt->execute([$histId]);
    $entry = $stmt->fetch();
    if (!$entry) fail('Registro no encontrado', 404);
    if ($entry['action'] !== 'deleted') fail('Solo se pueden restaurar tareas eliminadas');
    $db->exec("CREATE TABLE IF NOT EXISTS tasks (
        id          INTEGER PRIMARY KEY AUTOINCREMENT,
        title       TEXT    NOT NULL,
        description TEXT    DEFAULT '',
        priority    TEXT    DEFAULT 'media',
        status      TEXT    DEFAULT 'pendiente',
        category    TEXT    DEFAULT 'General',
        due_date    TEXT    DEFAULT '',
        created_at  TEXT    NOT NULL,
        updated_at  TEXT    NOT NULL
    )");
    $now = date('Y-m-d H:i:s');
    $ins = $db->prepare("INSERT INTO tasks
        (title,description,priority,status,category,due_date,created_at,updated_at)
        VALUES (?,?,?,?,?,?,?,?)");
    $ins->execute([
        $entry['task_title'],
        $entry['task_description'] ?? '',
        $entry['task_priority']    ?? 'media',
        'pendiente',
        $entry['task_category']    ?? 'General',
        $entry['task_due_date']    ?? '',
        $now,
        $now,
    ]);
    $newId = $db->lastInsertId();
    $db->prepare('DELETE FROM history WHERE id = ?')->execute([$histId]);
    ok(['new_task_id' => $newId], 'Tarea restaurada correctamente');
}
if ($method === 'DELETE') {
    $db->exec('DELETE FROM history');
    ok(null, 'Historial limpiado');
}
fail('Método no permitido', 405);
?>
