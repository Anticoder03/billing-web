<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Create bill';

$db = new Database();
$conn = $db->connect();

// Get all customers
$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();
        
        // Get form data
        $customer_id = $_POST['customer_id'];
        $bill_date = $_POST['bill_date'];
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
        
        // Generate bill number
        $bill_number = getNextbillNumber($conn);
        
        // Insert bill
        $stmt = $conn->prepare("INSERT INTO bills (bill_number, customer_id, bill_date, due_date, 
            subtotal, tax_rate, tax_amount, discount_amount, total_amount, status, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $bill_number,
            $customer_id,
            $bill_date,
            $due_date,
            $subtotal,
            $tax_rate,
            $totals['tax_amount'],
            $discount,
            $totals['total'],
            $status,
            $notes
        ]);
        
        $bill_id = $conn->lastInsertId();
        
        // Insert bill items
        $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, description, quantity, unit_price, amount) 
            VALUES (?, ?, ?, ?, ?)");
        
        foreach ($_POST['items'] as $item) {
            $amount = floatval($item['quantity']) * floatval($item['price']);
            $stmt->execute([
                $bill_id,
                $item['description'],
                $item['quantity'],
                $item['price'],
                $amount
            ]);
        }
        
        $conn->commit();
        
        setFlashMessage('success', 'bill created successfully!');
        header('Location: view.php?id=' . $bill_id);
        exit();
        
    } catch (Exception $e) {
        $conn->rollBack();
        setFlashMessage('error', 'Error creating bill: ' . $e->getMessage());
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
            Create New bill
        </h2>
        <a href="index.php" class="text-blue-600 hover:text-blue-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to bills
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
                    <option value="">Select Customer</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-flag text-blue-600 mr-2"></i>Status *
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="draft">Draft</option>
                    <option value="sent">Sent</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
            
            <div>
                <label for="bill_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-blue-600 mr-2"></i>bill Date *
                </label>
                <input type="date" name="bill_date" id="bill_date" required
                       value="<?php echo date('Y-m-d'); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar-check text-blue-600 mr-2"></i>Due Date *
                </label>
                <input type="date" name="due_date" id="due_date" required
                       value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <!-- Line Items -->
        <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
                <label class="block text-sm font-semibold text-gray-700">
                    <i class="fas fa-list text-blue-600 mr-2"></i>bill Items
                </label>
                <button type="button" onclick="addLineItem()" 
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Item
                </button>
            </div>
            
            <div id="line-items-container">
                <!-- First line item -->
                <div class="line-item grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="col-span-12 md:col-span-5">
                        <input type="text" name="items[1][description]" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Item description" required>
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <input type="number" name="items[1][quantity]" 
                               class="item-quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Qty" step="0.01" value="1" required onchange="updateLineItem(this)">
                    </div>
                    <div class="col-span-4 md:col-span-2">
                        <input type="number" name="items[1][price]" 
                               class="item-price w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="Price" step="0.01" required onchange="updateLineItem(this)">
                    </div>
                    <div class="col-span-3 md:col-span-2 flex items-center">
                        <span class="item-amount font-semibold text-gray-700">₹0.00</span>
                    </div>
                    <div class="col-span-1 md:col-span-1 flex items-center">
                        <button type="button" onclick="removeLineItem(this)" 
                                class="text-red-600 hover:text-red-800 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
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
                          placeholder="Additional notes..."></textarea>
            </div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Subtotal:</span>
                    <span id="subtotal" class="text-xl font-bold text-gray-800">₹0.00</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <label for="tax_rate" class="font-semibold text-gray-700">Tax Rate (%):</label>
                    <input type="number" name="tax_rate" id="tax_rate" step="0.01" value="0"
                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="calculateTotals()">
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-700">Tax Amount:</span>
                    <span id="tax_amount" class="text-xl font-bold text-gray-800">₹0.00</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <label for="discount" class="font-semibold text-gray-700">Discount:</label>
                    <input type="number" name="discount" id="discount" step="0.01" value="0"
                           class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           onchange="calculateTotals()">
                </div>
                
                <div class="flex justify-between items-center pt-4 border-t-2 border-gray-300">
                    <span class="text-xl font-bold text-gray-800">Total:</span>
                    <span id="total" class="text-2xl font-bold text-blue-600">₹0.00</span>
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Create bill
            </button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
