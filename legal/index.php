<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Legal Documents';

$db = new Database();
$conn = $db->connect();

// Get all legal documents
$stmt = $conn->query("SELECT ld.*, c.name as customer_name 
    FROM legal_documents ld 
    LEFT JOIN customers c ON ld.customer_id = c.id 
    ORDER BY ld.created_at DESC");
$documents = $stmt->fetchAll();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-file-contract text-cyan-600 mr-2"></i>
        Legal Documents
    </h2>
    <a href="create.php" class="bg-gradient-to-r from-cyan-600 to-cyan-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-cyan-700 hover:to-cyan-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
        <i class="fas fa-plus mr-2"></i>Create Document
    </a>
</div>

<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-hover">
            <thead class="bg-gradient-to-r from-cyan-500 to-cyan-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left font-semibold">Document #</th>
                    <th class="px-6 py-4 text-left font-semibold">Title</th>
                    <th class="px-6 py-4 text-left font-semibold">Type</th>
                    <th class="px-6 py-4 text-left font-semibold">Customer</th>
                    <th class="px-6 py-4 text-left font-semibold">Date</th>
                    <th class="px-6 py-4 text-left font-semibold">Status</th>
                    <th class="px-6 py-4 text-center font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                            <p class="text-lg">No legal documents found</p>
                            <a href="create.php" class="text-cyan-600 hover:text-cyan-800 mt-2 inline-block">Create your first document</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $doc): ?>
                        <tr class="hover:bg-cyan-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-cyan-600"><?php echo $doc['document_number']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo $doc['title']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $doc['document_type']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo $doc['customer_name']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo formatDate($doc['document_date']); ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $statusClass = [
                                    'signed' => 'badge-success',
                                    'draft' => 'badge-secondary',
                                    'cancelled' => 'badge-danger'
                                ];
                                ?>
                                <span class="badge <?php echo $statusClass[$doc['status']] ?? 'badge-secondary'; ?>">
                                    <?php echo ucfirst($doc['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="view.php?id=<?php echo $doc['id']; ?>" 
                                       class="bg-cyan-100 text-cyan-600 px-3 py-2 rounded-lg hover:bg-cyan-200 transition-colors" 
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $doc['id']; ?>" 
                                       class="bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors" 
                                       title="Delete"
                                       onclick="return confirmDelete('Are you sure you want to delete this document?')">
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
