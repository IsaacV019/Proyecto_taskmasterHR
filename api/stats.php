<?php
header('Content-Type: application/json; charset=utf-8');

// Conexión a la base de datos SQLite
function getDB() {
    $pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

try {
    $db = getDB();

    // Conteos generales por estado y prioridad
    $stats = $db->query("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN status='pendiente'   THEN 1 ELSE 0 END) as pendiente,
            SUM(CASE WHEN status='en progreso' THEN 1 ELSE 0 END) as en_progreso,
            SUM(CASE WHEN status='completada'  THEN 1 ELSE 0 END) as completada,
            SUM(CASE WHEN priority='alta'  THEN 1 ELSE 0 END) as alta,
            SUM(CASE WHEN priority='media' THEN 1 ELSE 0 END) as media,
            SUM(CASE WHEN priority='baja'  THEN 1 ELSE 0 END) as baja
        FROM tasks
    ")->fetch();

    // Conteo de tareas agrupadas por categoría
    $byCat = $db->query("
        SELECT category, COUNT(*) as cnt
        FROM tasks
        GROUP BY category
        ORDER BY cnt DESC
    ")->fetchAll();

    // Tareas vencidas: usa DATE('now') de SQLite para evitar diferencias de zona horaria con PHP
    $overdue = $db->query("
        SELECT COUNT(*) as n
        FROM tasks
        WHERE due_date != ''
          AND due_date < DATE('now')
          AND status != 'completada'
    ")->fetch()['n'];

    // Últimas 5 tareas creadas para mostrar en el dashboard
    $recent = $db->query("
        SELECT id, title, priority, status, due_date
        FROM tasks
        ORDER BY created_at DESC
        LIMIT 5
    ")->fetchAll();

    // Respuesta JSON con todas las estadísticas
    echo json_encode([
        'success' => true,
        'data' => [
            'total'       => (int)$stats['total'],
            'pendiente'   => (int)$stats['pendiente'],
            'en_progreso' => (int)$stats['en_progreso'],
            'completada'  => (int)$stats['completada'],
            'by_priority' => [
                'alta'  => (int)$stats['alta'],
                'media' => (int)$stats['media'],
                'baja'  => (int)$stats['baja'],
            ],
            'by_category' => $byCat,
            'overdue'     => (int)$overdue,
            'recent'      => $recent,
        ]
    ]);

} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
}
?>
