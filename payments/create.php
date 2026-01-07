<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Record Payment';

$db = new Database();
$conn = $db->connect();

// Get all customers
$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll();

// Get unpaid invoices
$invoices = $conn->query("SELECT id, invoice_number, total_amount FROM invoices WHERE status != 'paid' ORDER BY invoice_date DESC")->fetchAll();

// Get unpaid bills
$bills = $conn->query("SELECT id, bill_number, total_amount FROM bills WHERE status != 'paid' ORDER BY bill_date DESC")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = $_POST['customer_id'];
        $payment_date = $_POST['payment_date'];
        $amount = floatval($_POST['amount']);
        $payment_method = $_POST['payment_method'];
        $reference_number = $_POST['reference_number'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $invoice_id = !empty($_POST['invoice_id']) ? $_POST['invoice_id'] : null;
        $bill_id = !empty($_POST['bill_id']) ? $_POST['bill_id'] : null;
        
        // Generate payment number
        $payment_number = getNextPaymentNumber($conn);
        
        // Insert payment
        $stmt = $conn->prepare("INSERT INTO payments (payment_number, customer_id, invoice_id, bill_id, 
            payment_date, amount, payment_method, reference_number, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $payment_number,
            $customer_id,
            $invoice_id,
            $bill_id,
            $payment_date,
            $amount,
            $payment_method,
            $reference_number,
            $notes
        ]);
        
        // Update invoice/bill status if fully paid
        if ($invoice_id) {
            $stmt = $conn->prepare("SELECT total_amount FROM invoices WHERE id = ?");
            $stmt->execute([$invoice_id]);
            $invoice = $stmt->fetch();
            
            if ($invoice && $amount >= $invoice['total_amount']) {
                $stmt = $conn->prepare("UPDATE invoices SET status = 'paid' WHERE id = ?");
                $stmt->execute([$invoice_id]);
            }
        }
        
        if ($bill_id) {
            $stmt = $conn->prepare("SELECT total_amount FROM bills WHERE id = ?");
            $stmt->execute([$bill_id]);
            $bill = $stmt->fetch();
            
            if ($bill && $amount >= $bill['total_amount']) {
                $stmt = $conn->prepare("UPDATE bills SET status = 'paid' WHERE id = ?");
                $stmt->execute([$bill_id]);
            }
        }
        
        setFlashMessage('success', 'Payment recorded successfully!');
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error recording payment: ' . $e->getMessage());
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle text-green-600 mr-2"></i>
            Record New Payment
        </h2>
        <a href="index.php" class="text-green-600 hover:text-green-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Payments
        </a>
    </div>
    
    <form method="POST" action="" class="bg-white rounded-xl shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-green-600 mr-2"></i>Customer *
                </label>
                <select name="customer_id" id="customer_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Select Customer</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="payment_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-green-600 mr-2"></i>Payment Date *
                </label>
                <input type="date" name="payment_date" id="payment_date" required
                       value="<?php echo date('Y-m-d'); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-rupee-sign text-green-600 mr-2"></i>Amount *
                </label>
                <input type="number" name="amount" id="amount" step="0.01" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="0.00">
            </div>
            
            <div>
                <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-credit-card text-green-600 mr-2"></i>Payment Method *
                </label>
                <select name="payment_method" id="payment_method" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="other">Other</option>
                </select>
            </div>
            
            <div>
                <label for="invoice_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-file-invoice text-green-600 mr-2"></i>Related Invoice (Optional)
                </label>
                <select name="invoice_id" id="invoice_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">None</option>
                    <?php foreach ($invoices as $invoice): ?>
                        <option value="<?php echo $invoice['id']; ?>">
                            <?php echo $invoice['invoice_number']; ?> - <?php echo formatCurrency($invoice['total_amount']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="bill_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-receipt text-green-600 mr-2"></i>Related Bill (Optional)
                </label>
                <select name="bill_id" id="bill_id"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">None</option>
                    <?php foreach ($bills as $bill): ?>
                        <option value="<?php echo $bill['id']; ?>">
                            <?php echo $bill['bill_number']; ?> - <?php echo formatCurrency($bill['total_amount']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label for="reference_number" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-hashtag text-green-600 mr-2"></i>Reference Number
                </label>
                <input type="text" name="reference_number" id="reference_number"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                       placeholder="Transaction ID, Check number, etc.">
            </div>
            
            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note text-green-600 mr-2"></i>Notes
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                          placeholder="Additional notes..."></textarea>
            </div>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Record Payment
            </button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
