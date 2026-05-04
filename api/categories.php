<?php
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