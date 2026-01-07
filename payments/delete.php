<?php
require_once '../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->connect();

$payment_id = $_GET['id'] ?? 0;

if ($payment_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->execute([$payment_id]);
        
        setFlashMessage('success', 'Payment deleted successfully!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error deleting payment: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
