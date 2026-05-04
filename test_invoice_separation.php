<?php
$baseUrl = 'http://localhost:8000/api';

// 1. LOGIN
echo "📌 STEP 1: LOGIN\n";
$ch = curl_init("$baseUrl/login");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'superadmin@fitart.co.id',
    'password' => 'superadmin123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);

if (isset($data['token'])) {
    $token = $data['token'];
    echo "✅ Login berhasil!\n";
    echo "   Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Login gagal!\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n";
    exit;
}

curl_close($ch);

// 2. GET INVOICE LICENSE
echo "📌 STEP 2: GET /api/invoice-license\n";
$ch = curl_init("$baseUrl/invoice-license");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);

echo "Total invoice LICENSE: " . $data['total'] . "\n";
if ($data['total'] > 0) {
    echo "Invoice LICENSE:\n";
    foreach ($data['data'] as $inv) {
        echo "  - #" . $inv['number'] . " | " . $inv['client'] . " | Rp " . number_format($inv['jumlah']) . "\n";
    }
} else {
    echo "❌ Tidak ada invoice license\n";
}
echo "\n";

curl_close($ch);

// 3. GET INVOICE PRODUCT
echo "📌 STEP 3: GET /api/invoice-products\n";
$ch = curl_init("$baseUrl/invoice-products");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$data = json_decode($response, true);

echo "Total invoice PRODUCT: " . $data['total'] . "\n";
if ($data['total'] > 0) {
    echo "Invoice PRODUCT:\n";
    foreach ($data['data'] as $inv) {
        echo "  - #" . $inv['number'] . " | " . $inv['client'] . " | Rp " . number_format($inv['netto']) . "\n";
    }
} else {
    echo "❌ Tidak ada invoice product\n";
}
echo "\n";

curl_close($ch);

echo "✅ SELESAI!\n";
?>
