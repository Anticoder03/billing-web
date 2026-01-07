<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'All Customers';

$db = new Database();
$conn = $db->connect();

// Get all customers
$stmt = $conn->query("SELECT * FROM customers ORDER BY name");
$customers = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-users text-indigo-600 mr-2"></i>
        All Customers
    </h2>
    <a href="create.php" class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Add New Customer
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-hover">
            <thead class="bg-gradient-to-r from-indigo-500 to-indigo-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold">Name</th>
                    <th class="px-6 py-4 text-left font-semibold">Email</th>
                    <th class="px-6 py-4 text-left font-semibold">Phone</th>
                    <th class="px-6 py-4 text-left font-semibold">City</th>
                    <th class="px-6 py-4 text-left font-semibold">Country</th>
                    <th class="px-6 py-4 text-center font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No customers found</p>
                            <a href="create.php" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">Add your first customer</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                        <tr class="hover:bg-indigo-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-800"><?php echo $customer['name']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $customer['email'] ?? '-'; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $customer['phone'] ?? '-'; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $customer['city'] ?? '-'; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $customer['country'] ?? '-'; ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="edit.php?id=<?php echo $customer['id']; ?>" 
                                       class="bg-yellow-100 text-yellow-600 px-3 py-2 rounded-lg hover:bg-yellow-200 transition-colors" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $customer['id']; ?>" 
                                       class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors" 
                                       title="Delete"
                                       onclick="return confirmDelete('Are you sure you want to delete this customer?')">
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
