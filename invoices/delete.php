<?php
require_once '../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->connect();

$invoice_id = $_GET['id'] ?? 0;

if ($invoice_id) {
    try {
        // Delete invoice (cascade will delete items)
        $stmt = $conn->prepare("DELETE FROM invoices WHERE id = ?");
        $stmt->execute([$invoice_id]);
        
        setFlashMessage('success', 'Invoice deleted successfully!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error deleting invoice: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
