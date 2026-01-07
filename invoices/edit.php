<?php
// Invoice edit page - similar to create but pre-filled with existing data
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Edit Invoice';

$db = new Database();
$conn = $db->connect();

$invoice_id = $_GET['id'] ?? 0;

// Get invoice details
$stmt = $conn->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$invoice_id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    header('Location: index.php');
    exit();
}

// Get invoice items
$stmt = $conn->prepare("SELECT * FROM invoice_items WHERE invoice_id = ?");
$stmt->execute([$invoice_id]);
$items = $stmt->fetchAll();

// Get all customers
$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        // Get form data
        $customer_id = $_POST['customer_id'];
        $invoice_date = $_POST['invoice_date'];
        $due_date = $_POST['due_date'];
        $tax_rate = floatval($_POST['tax_rate']);
        $discount = floatval($_POST['discount']);
        $notes = $_POST['notes'] ?? '';
        $status = $_POST['status'];
        
        // Calculate totals
        $subtotal = 0;
        foreach ($_POST['items'] as $item) {
            $subtotal += floatval($item['quantity']) * floatval($item['price']);
        }
        
        $totals = calculateTotals($subtotal, $tax_rate, $discount);
        
        // Update invoice
        $stmt = $conn->prepare("UPDATE invoices SET customer_id = ?, invoice_date = ?, due_date = ?, 
            subtotal = ?, tax_rate = ?, tax_amount = ?, discount_amount = ?, total_amount = ?, status = ?, notes = ?
            WHERE id = ?");
        
        $stmt->execute([
            $customer_id,
            $invoice_date,
            $due_date,
            $subtotal,
            $tax_rate,
            $totals['tax_amount'],
            $discount,
            $totals['total'],
            $status,
            $notes,
            $invoice_id
        ]);
        
        // Delete old items
        $stmt = $conn->prepare("DELETE FROM invoice_items WHERE invoice_id = ?");
        $stmt->execute([$invoice_id]);
        
        // Insert new items
        $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, amount) 
            VALUES (?, ?, ?, ?, ?)");
        
        foreach ($_POST['items'] as $item) {
            $amount = floatval($item['quantity']) * floatval($item['price']);
            $stmt->execute([
                $invoice_id,
                $item['description'],
                $item['quantity'],
                $item['price'],
                $amount
            ]);
        }
        
        $conn->commit();
        
        setFlashMessage('success', 'Invoice updated successfully!');
        header('Location: view.php?id=' . $invoice_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        setFlashMessage('error', 'Error updating invoice: ' . $e->getMessage());
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit text-blue-600 mr-2"></i>
            Edit Invoice: <?php echo $invoice['invoice_number']; ?>
        </h2>
        <a href="view.php?id=<?php echo $invoice_id; ?>" class="text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Invoice
        </a>
    </div>
    
    <form method="POST" action="" class="bg-white rounded-xl shadow-lg p-8">
        <!-- Customer & Date Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-blue-600 mr-2"></i>Customer *
                </label>
                <select name="customer_id" id="customer_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>" <?php echo $customer['id'] == $invoice['customer_id'] ? 'selected' : ''; ?>>
                            <?php echo $customer['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-flag text-blue-600 mr-2"></i>Status *
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="draft" <?php echo $invoice['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="sent" <?php echo $invoice['status'] == 'sent' ? 'selected' : ''; ?>>Sent</option>
                    <option value="paid" <?php echo $invoice['status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                    <option value="overdue" <?php echo $invoice['status'] == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                    <option value="cancelled" <?php echo $invoice['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            
            <div>
                <label for="invoice_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>Invoice Date *
                </label>
                <input type="date" name="invoice_date" id="invoice_date" required
                       value="<?php echo $invoice['invoice_date']; ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>Due Date *
                </label>
                <input type="date" name="due_date" id="due_date" required
                       value="<?php echo $invoice['due_date']; ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <!-- Line Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <label class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-list text-blue-600 mr-2"></i>Invoice Items
                </label>
                <button type="button" onclick="addLineItem()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>
            
            <div id="line-items-container">
                <?php foreach ($items as $index => $item): ?>
                    <div class="line-item grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="col-span-12 md:col-span-5">
                            <input type="text" name="items[<?php echo $index + 1; ?>][description]" 
                                   value="<?php echo htmlspecialchars($item['description']); ?>"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Item description" required>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <input type="number" name="items[<?php echo $index + 1; ?>][quantity]" 
                                   value="<?php echo $item['quantity']; ?>"
                                   class="item-quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Qty" step="0.01" required onchange="updateLineItem(this)">
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <input type="number" name="items[<?php echo $index + 1; ?>][price]" 
                                   value="<?php echo $item['unit_price']; ?>"
                                   class="item-price w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                                   placeholder="Price" step="0.01" required onchange="updateLineItem(this)">
                        </div>
                        <div class="col-span-3 md:col-span-2 flex items-center">
                            <span class="item-amount font-semibold text-gray-700">₹<?php echo number_format($item['amount'], 2); ?></span>
                        </div>
                        <div class="col-span-1 md:col-span-1 flex items-center">
                            <button type="button" onclick="removeLineItem(this)" 
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Totals -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-sticky-note text-blue-600 mr-2"></i>Notes
                </label>
                <textarea name="notes" id="notes" rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Additional notes..."><?php echo htmlspecialchars($invoice['notes']); ?></textarea>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Subtotal:</span>
                    <span id="subtotal" class="text-xl font-bold text-gray-800">₹<?php echo number_format($invoice['subtotal'], 2); ?></span>
                </div>
                
                <div class="flex justify-between items-center">
                    <label for="tax_rate" class="font-semibold text-gray-700">Tax Rate (%):</label>
                    <input type="number" name="tax_rate" id="tax_rate" step="0.01" value="<?php echo $invoice['tax_rate']; ?>"
                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="calculateTotals()">
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Tax Amount:</span>
                    <span id="tax_amount" class="text-xl font-bold text-gray-800">₹<?php echo number_format($invoice['tax_amount'], 2); ?></span>
                </div>
                
                <div class="flex justify-between items-center">
                    <label for="discount" class="font-semibold text-gray-700">Discount:</label>
                    <input type="number" name="discount" id="discount" step="0.01" value="<?php echo $invoice['discount_amount']; ?>"
                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="calculateTotals()">
                </div>
                
                <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                    <span class="text-xl font-bold text-gray-800">Total:</span>
                    <span id="total" class="text-2xl font-bold text-blue-600">₹<?php echo number_format($invoice['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="view.php?id=<?php echo $invoice_id; ?>" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Update Invoice
            </button>
        </div>
    </form>
</div>

<script>
// Initialize totals on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>

<?php include '../includes/footer.php'; ?>
