<?php// la neta no se a ver termina esto isaac , recurda quitar mis cosas desde el git.
header('Content-Type: application/json; charset=utf-8');
$pdo = new PDO('sqlite:' . __DIR__ . '/../db/taskmaster.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['success'=>true,'data'=>$cats]);
?>
