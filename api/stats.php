<?php
// Indica que la respuesta será en formato JSON y con codificación UTF-8
header('Content-Type: application/json; charset=utf-8');

<<<<<<< Alex-Javier
// Función para conectar a la base de datos SQLite
=======
// Conexión a la base de datos SQLite
>>>>>>> main
function getDB() {
    // Crea la conexión a la base de datos
    $pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');
    
    // Configura que los errores se muestren como excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura que los resultados se devuelvan como arreglos asociativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo; // Retorna la conexión
}

try {
    // Obtiene la conexión a la base de datos
    $db = getDB();

<<<<<<< Alex-Javier
    // Consulta para obtener estadísticas generales de las tareas
=======
    // Conteos generales por estado y prioridad
>>>>>>> main
    $stats = $db->query("
        SELECT
            COUNT(*) as total, -- Total de tareas
            SUM(CASE WHEN status='pendiente'   THEN 1 ELSE 0 END) as pendiente, -- Tareas pendientes
            SUM(CASE WHEN status='en progreso' THEN 1 ELSE 0 END) as en_progreso, -- Tareas en progreso
            SUM(CASE WHEN status='completada'  THEN 1 ELSE 0 END) as completada, -- Tareas completadas
            SUM(CASE WHEN priority='alta'  THEN 1 ELSE 0 END) as alta, -- Prioridad alta
            SUM(CASE WHEN priority='media' THEN 1 ELSE 0 END) as media, -- Prioridad media
            SUM(CASE WHEN priority='baja'  THEN 1 ELSE 0 END) as baja -- Prioridad baja
        FROM tasks
    ")->fetch(); // Obtiene un solo resultado

<<<<<<< Alex-Javier
    // Consulta para contar tareas por categoría
    $byCat = $db->query("
        SELECT category, COUNT(*) as cnt 
        FROM tasks 
        GROUP BY category 
        ORDER BY cnt DESC
    ")->fetchAll(); // Obtiene todos los resultados

    // Obtiene la fecha actual
    $today = date('Y-m-d');

    // Cuenta tareas vencidas (fecha menor a hoy y no completadas)
    $overdue = $db->query("
        SELECT COUNT(*) as n 
        FROM tasks 
        WHERE due_date != '' 
        AND due_date < '$today' 
        AND status != 'completada'
    ")->fetch()['n'];

    // Obtiene las 5 tareas más recientes
    $recent = $db->query("
        SELECT id, title, priority, status, due_date 
        FROM tasks 
        ORDER BY created_at DESC 
        LIMIT 5
    ")->fetchAll();

    // Devuelve la respuesta en formato JSON con todos los datos
=======
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
>>>>>>> main
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
            'by_category' => $byCat, // Tareas agrupadas por categoría
            'overdue'     => (int)$overdue, // Tareas vencidas
            'recent'      => $recent, // Tareas recientes
        ]
    ]);

} catch(Exception $e) {
<<<<<<< Alex-Javier
    // En caso de error, devuelve un JSON con el mensaje
    echo json_encode([
        'success'=>false,
        'message'=>$e->getMessage(),
        'data'=>null
    ]);
=======
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
>>>>>>> main
}
?>