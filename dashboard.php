<?php
require_once 'config/config.php';
requireLogin(); // Auto-login enabled for testing

$pageTitle = 'Dashboard';

// Get database connection
$db = new Database();
$conn = $db->connect();

// Get statistics
$stats = [
    'total_invoices' => 0,
    'total_bills' => 0,
    'total_payments' => 0,
    'pending_amount' => 0
];

// Count invoices
$stmt = $conn->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM invoices");
$result = $stmt->fetch();
$stats['total_invoices'] = $result['count'];

// Count bills
$stmt = $conn->query("SELECT COUNT(*) as count, SUM(total_amount) as total FROM bills");
$result = $stmt->fetch();
$stats['total_bills'] = $result['count'];

// Count payments
$stmt = $conn->query("SELECT COUNT(*) as count, SUM(amount) as total FROM payments");
$result = $stmt->fetch();
$stats['total_payments'] = $result['count'];
$stats['total_paid'] = $result['total'] ?? 0;

// Calculate pending amount (invoices + bills - payments)
$stmt = $conn->query("SELECT 
    (SELECT COALESCE(SUM(total_amount), 0) FROM invoices WHERE status != 'paid') +
    (SELECT COALESCE(SUM(total_amount), 0) FROM bills WHERE status != 'paid') as pending");
$result = $stmt->fetch();
$stats['pending_amount'] = $result['pending'];

// Get recent invoices
$recentInvoices = $conn->query("SELECT i.*, c.name as customer_name 
    FROM invoices i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    ORDER BY i.created_at DESC LIMIT 5")->fetchAll();

// Get recent bills
$recentBills = $conn->query("SELECT b.*, c.name as customer_name 
    FROM bills b 
    LEFT JOIN customers c ON b.customer_id = c.id 
    ORDER BY b.created_at DESC LIMIT 5")->fetchAll();

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Invoices -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-invoice text-2xl"></i>
            </div>
            <span class="text-3xl font-bold"><?php echo $stats['total_invoices']; ?></span>
        </div>
        <h3 class="text-lg font-semibold">Total Invoices</h3>
        <p class="text-blue-100 text-sm mt-1">All time invoices</p>
    </div>
    
    <!-- Total Bills -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-receipt text-2xl"></i>
            </div>
            <span class="text-3xl font-bold"><?php echo $stats['total_bills']; ?></span>
        </div>
        <h3 class="text-lg font-semibold">Total Bills</h3>
        <p class="text-purple-100 text-sm mt-1">All time bills</p>
    </div>
    
    <!-- Total Payments -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
            <span class="text-3xl font-bold"><?php echo $stats['total_payments']; ?></span>
        </div>
        <h3 class="text-lg font-semibold">Total Payments</h3>
        <p class="text-green-100 text-sm mt-1"><?php echo formatCurrency($stats['total_paid']); ?> received</p>
    </div>
    
    <!-- Pending Amount -->
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white card-hover">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <span class="text-2xl font-bold"><?php echo formatCurrency($stats['pending_amount']); ?></span>
        </div>
        <h3 class="text-lg font-semibold">Pending Amount</h3>
        <p class="text-orange-100 text-sm mt-1">Awaiting payment</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="<?php echo BASE_URL; ?>invoices/create.php" 
       class="bg-white rounded-lg shadow-md p-4 hover:shadow-xl transition-all duration-200 flex items-center space-x-3 group">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
            <i class="fas fa-plus text-blue-600"></i>
        </div>
        <span class="font-semibold text-gray-700">Create Invoice</span>
    </a>
    
    <a href="<?php echo BASE_URL; ?>bills/create.php" 
       class="bg-white rounded-lg shadow-md p-4 hover:shadow-xl transition-all duration-200 flex items-center space-x-3 group">
        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
            <i class="fas fa-plus text-purple-600"></i>
        </div>
        <span class="font-semibold text-gray-700">Create Bill</span>
    </a>
    
    <a href="<?php echo BASE_URL; ?>payments/create.php" 
       class="bg-white rounded-lg shadow-md p-4 hover:shadow-xl transition-all duration-200 flex items-center space-x-3 group">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
            <i class="fas fa-money-bill-wave text-green-600"></i>
        </div>
        <span class="font-semibold text-gray-700">Record Payment</span>
    </a>
    
    <a href="<?php echo BASE_URL; ?>legal/create.php" 
       class="bg-white rounded-lg shadow-md p-4 hover:shadow-xl transition-all duration-200 flex items-center space-x-3 group">
        <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center group-hover:bg-cyan-200 transition-colors">
            <i class="fas fa-file-contract text-cyan-600"></i>
        </div>
        <span class="font-semibold text-gray-700">New Document</span>
    </a>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Invoices -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-file-invoice mr-2"></i>
                Recent Invoices
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentInvoices)): ?>
                <p class="text-gray-500 text-center py-8">No invoices yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentInvoices as $invoice): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <p class="font-semibold text-gray-800"><?php echo $invoice['invoice_number']; ?></p>
                                <p class="text-sm text-gray-600"><?php echo $invoice['customer_name']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-blue-600"><?php echo formatCurrency($invoice['total_amount']); ?></p>
                                <span class="badge badge-<?php echo $invoice['status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($invoice['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Bills -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Recent Bills
            </h3>
        </div>
        <div class="p-6">
            <?php if (empty($recentBills)): ?>
                <p class="text-gray-500 text-center py-8">No bills yet</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentBills as $bill): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div>
                                <p class="font-semibold text-gray-800"><?php echo $bill['bill_number']; ?></p>
                                <p class="text-sm text-gray-600"><?php echo $bill['customer_name']; ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-purple-600"><?php echo formatCurrency($bill['total_amount']); ?></p>
                                <span class="badge badge-<?php echo $bill['status'] === 'paid' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($bill['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
