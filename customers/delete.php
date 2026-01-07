<?php
require_once '../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->connect();

$customer_id = $_GET['id'] ?? 0;

if ($customer_id) {
    try {
        // Delete customer (cascade will delete related invoices, bills, etc.)
        $stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
        $stmt->execute([$customer_id]);
        
        setFlashMessage('success', 'Customer deleted successfully!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error deleting customer: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
