<?php
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
