<?php
<<<<<<< Alex-Javier
// <?php → Indica el inicio de código PHP

// header() → Define el tipo de respuesta que enviará el servidor (JSON en este caso)
header('Content-Type: application/json; charset=utf-8');

// new PDO() → Crea una conexión a la base de datos SQLite
$pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');

// setAttribute() → Configura el comportamiento de la conexión (errores como excepciones)
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// query() → Ejecuta una consulta SQL
// SELECT * → Selecciona todos los campos de la tabla
// ORDER BY name → Ordena los resultados por el campo "name"
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')
            // fetchAll() → Obtiene todos los resultados de la consulta
            // PDO::FETCH_ASSOC → Devuelve los datos como arreglo asociativo
            ->fetchAll(PDO::FETCH_ASSOC);

// json_encode() → Convierte el arreglo de PHP a formato JSON para enviarlo
echo json_encode([
    'success'=>true, // Indica que la operación fue exitosa
    'data'=>$cats    // Contiene los datos obtenidos de la base
]);

// ?> → Indica el final del código PHP
?>
=======
// Headers de respuesta y CORS (igual que los demás endpoints)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Responde inmediatamente a peticiones preflight de CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

try {
    // Conexión a la base de datos SQLite
    $pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Obtiene todas las categorías ordenadas alfabéticamente
    $stmt = $pdo->prepare('SELECT * FROM categories ORDER BY name');
    $stmt->execute();
    $cats = $stmt->fetchAll();

    // Respuesta JSON consistente con los demás endpoints
    echo json_encode(['success' => true, 'message' => 'OK', 'data' => $cats]);

} catch (Exception $e) {
    // Error controlado en formato JSON
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'data' => null]);
}
?>
>>>>>>> main
