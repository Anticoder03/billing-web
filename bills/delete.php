<?php
require_once '../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->connect();

$bill_id = $_GET['id'] ?? 0;

if ($bill_id) {
    try {
        // Delete bill (cascade will delete items)
        $stmt = $conn->prepare("DELETE FROM bills WHERE id = ?");
        $stmt->execute([$bill_id]);
        
        setFlashMessage('success', 'bill deleted successfully!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error deleting bill: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
