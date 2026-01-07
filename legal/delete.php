<?php
require_once '../config/config.php';
requireLogin();

$db = new Database();
$conn = $db->connect();

$document_id = $_GET['id'] ?? 0;

if ($document_id) {
    try {
        $stmt = $conn->prepare("DELETE FROM legal_documents WHERE id = ?");
        $stmt->execute([$document_id]);
        
        setFlashMessage('success', 'Legal document deleted successfully!');
    } catch (Exception $e) {
        setFlashMessage('error', 'Error deleting document: ' . $e->getMessage());
    }
}

header('Location: index.php');
exit();
?>
