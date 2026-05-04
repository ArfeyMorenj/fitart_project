<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=fitart_project', 'root', '');

// Update invoice type 7 dari is_license = 1 ke 0
$stmt = $pdo->prepare('UPDATE invoice_types SET is_license = 0 WHERE id = 7');
$result = $stmt->execute();

if ($result) {
    echo "✅ SUCCESS: Invoice Type 7 updated!\n";
    echo "   is_license changed from 1 to 0\n\n";
    
    // Verify
    $stmt = $pdo->query('SELECT id, code, name, is_license FROM invoice_types WHERE id = 7');
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Updated data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    echo "❌ ERROR: Update failed!\n";
}
?>
