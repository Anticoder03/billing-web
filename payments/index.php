<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Payments';

$db = new Database();
$conn = $db->connect();

// Get all payments with customer and invoice/bill info
$stmt = $conn->query("SELECT p.*, c.name as customer_name,
    i.invoice_number, b.bill_number
    FROM payments p 
    LEFT JOIN customers c ON p.customer_id = c.id 
    LEFT JOIN invoices i ON p.invoice_id = i.id
    LEFT JOIN bills b ON p.bill_id = b.id
    ORDER BY p.created_at DESC");
$payments = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
        All Payments
    </h2>
    <a href="create.php" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Record Payment
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-hover">
            <thead class="bg-gradient-to-r from-green-500 to-green-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold">Payment #</th>
                    <th class="px-6 py-4 text-left font-semibold">Customer</th>
                    <th class="px-6 py-4 text-left font-semibold">Reference</th>
                    <th class="px-6 py-4 text-left font-semibold">Date</th>
                    <th class="px-6 py-4 text-left font-semibold">Amount</th>
                    <th class="px-6 py-4 text-left font-semibold">Method</th>
                    <th class="px-6 py-4 text-center font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No payments found</p>
                            <a href="create.php" class="text-green-600 hover:text-green-800 mt-2 inline-block">Record your first payment</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr class="hover:bg-green-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-green-600"><?php echo $payment['payment_number']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo $payment['customer_name']; ?></td>
                            <td class="px-6 py-4 text-gray-600">
                                <?php 
                                if ($payment['invoice_number']) {
                                    echo 'Invoice: ' . $payment['invoice_number'];
                                } elseif ($payment['bill_number']) {
                                    echo 'Bill: ' . $payment['bill_number'];
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo formatDate($payment['payment_date']); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo formatCurrency($payment['amount']); ?></td>
                            <td class="px-6 py-4">
                                <span class="badge badge-info"><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="delete.php?id=<?php echo $payment['id']; ?>" 
                                       class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors" 
                                       title="Delete"
                                       onclick="return confirmDelete('Are you sure you want to delete this payment?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
