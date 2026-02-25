<?php
require_once '../includes/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if (empty($name) || empty($phone) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Lütfen zorunlu alanları doldurun.']);
    exit;
}

try {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $subject, $message]);

    echo json_encode(['success' => true, 'message' => 'Mesajınız başarıyla gönderildi. En kısa sürede dönüş yapacağız.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.']);
}
