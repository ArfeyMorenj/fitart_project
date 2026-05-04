<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=fitart_project', 'root', '');
$stmt = $pdo->query('SELECT id, code, name, is_license FROM invoice_types WHERE id IN (4, 7)');
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result, JSON_PRETTY_PRINT);
?>
