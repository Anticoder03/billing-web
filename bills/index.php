<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'All bills';

$db = new Database();
$conn = $db->connect();

// Get all bills with customer names
$stmt = $conn->query("SELECT i.*, c.name as customer_name 
    FROM bills i 
    LEFT JOIN customers c ON i.customer_id = c.id 
    ORDER BY i.created_at DESC");
$bills = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-file-bill text-blue-600 mr-2"></i>
        All bills
    </h2>
    <a href="create.php" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Create New bill
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-hover">
            <thead class="bg-gradient-to-r from-blue-500 to-purple-500 text-white">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold">bill #</th>
                    <th class="px-6 py-4 text-left font-semibold">Customer</th>
                    <th class="px-6 py-4 text-left font-semibold">Date</th>
                    <th class="px-6 py-4 text-left font-semibold">Due Date</th>
                    <th class="px-6 py-4 text-left font-semibold">Amount</th>
                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                    <th class="px-6 py-4 text-center font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($bills)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No bills found</p>
                            <a href="create.php" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Create your first bill</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($bills as $bill): ?>
                        <tr class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-blue-600"><?php echo $bill['bill_number']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo $bill['customer_name']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo formatDate($bill['bill_date']); ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo formatDate($bill['due_date']); ?></td>
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo formatCurrency($bill['total_amount']); ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClass = [
                                    'paid' => 'badge-success',
                                    'sent' => 'badge-info',
                                    'draft' => 'badge-secondary',
                                    'overdue' => 'badge-danger',
                                    'cancelled' => 'badge-warning'
                                ];
                                ?>
                                <span class="badge <?php echo $statusClass[$bill['status']] ?? 'badge-secondary'; ?>">
                                    <?php echo ucfirst($bill['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="view.php?id=<?php echo $bill['id']; ?>" 
                                       class="bg-blue-100 text-blue-600 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $bill['id']; ?>" 
                                       class="bg-yellow-100 text-yellow-600 px-3 py-2 rounded-lg hover:bg-yellow-200 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $bill['id']; ?>" 
                                       class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors" 
                                       title="Delete"
                                       onclick="return confirmDelete('Are you sure you want to delete this bill?')">
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
