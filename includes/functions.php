<?php
// Helper functions

// Sanitize input data
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if user is logged in (disabled for testing)
function isLoggedIn() {
    // Auto-login for testing - always return true
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['email'] = 'admin@billing.com';
        $_SESSION['full_name'] = 'Administrator';
    }
    return true;
}

// Redirect to login if not authenticated (disabled for testing)
function requireLogin() {
    // Auto-login for testing
    isLoggedIn();
}

// Format currency
function formatCurrency($amount) {
    return '₹' . number_format($amount, 2);
}

// Format date
function formatDate($date) {
    return date('d M, Y', strtotime($date));
}

// Generate unique number
function generateNumber($prefix, $lastNumber = 0) {
    $number = $lastNumber + 1;
    return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
}

// Get next invoice number
function getNextInvoiceNumber($conn) {
    $stmt = $conn->query("SELECT invoice_number FROM invoices ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        $lastNum = (int) substr($result['invoice_number'], 3);
        return generateNumber('INV', $lastNum);
    }
    return 'INV00001';
}

// Get next bill number
function getNextBillNumber($conn) {
    $stmt = $conn->query("SELECT bill_number FROM bills ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        $lastNum = (int) substr($result['bill_number'], 4);
        return generateNumber('BILL', $lastNum);
    }
    return 'BILL00001';
}

// Get next payment number
function getNextPaymentNumber($conn) {
    $stmt = $conn->query("SELECT payment_number FROM payments ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        $lastNum = (int) substr($result['payment_number'], 3);
        return generateNumber('PAY', $lastNum);
    }
    return 'PAY00001';
}

// Get next document number
function getNextDocumentNumber($conn) {
    $stmt = $conn->query("SELECT document_number FROM legal_documents ORDER BY id DESC LIMIT 1");
    $result = $stmt->fetch();
    
    if ($result) {
        $lastNum = (int) substr($result['document_number'], 3);
        return generateNumber('DOC', $lastNum);
    }
    return 'DOC00001';
}

// Flash message functions
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type;
    $_SESSION['flash_message'] = $message;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'];
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_type']);
        unset($_SESSION['flash_message']);
        return ['type' => $type, 'message' => $message];
    }
    return null;
}

// Get customer name by ID
function getCustomerName($conn, $customerId) {
    $stmt = $conn->prepare("SELECT name FROM customers WHERE id = ?");
    $stmt->execute([$customerId]);
    $result = $stmt->fetch();
    return $result ? $result['name'] : 'Unknown';
}

// Calculate totals
function calculateTotals($subtotal, $taxRate, $discount) {
    $taxAmount = ($subtotal * $taxRate) / 100;
    $total = $subtotal + $taxAmount - $discount;
    return [
        'tax_amount' => $taxAmount,
        'total' => $total
    ];
}
?>
