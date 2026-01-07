<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'View bill';

$db = new Database();
$conn = $db->connect();

// Get bill ID
$bill_id = $_GET['id'] ?? 0;

// Get bill details
$stmt = $conn->prepare("SELECT b.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, 
    c.address as customer_address, c.city, c.state, c.zip_code, c.country
    FROM bills b 
    LEFT JOIN customers c ON b.customer_id = c.id 
    WHERE b.id = ?");
$stmt->execute([$bill_id]);
$bill = $stmt->fetch();

if (!$bill) {
    header('Location: index.php');
    exit();
}

// Get bill items
$stmt = $conn->prepare("SELECT * FROM bill_items WHERE bill_id = ?");
$stmt->execute([$bill_id]);
$items = $stmt->fetchAll();

// Get company info
$company = $conn->query("SELECT * FROM company_info LIMIT 1")->fetch();

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<!-- PDF Generation Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6 no-print">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-file-bill text-blue-600 mr-2"></i>
            bill Details
        </h2>
        <div class="flex space-x-3">
            <button onclick="downloadPDF()" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-download mr-2"></i>Download PDF
            </button>
            <a href="edit.php?id=<?php echo $bill['id']; ?>" 
               class="bg-gradient-to-r from-yellow-600 to-yellow-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-yellow-700 hover:to-yellow-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <a href="delete.php?id=<?php echo $bill['id']; ?>" 
               onclick="return confirmDelete('Are you sure you want to delete this bill?')"
               class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-red-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-trash mr-2"></i>Delete
            </a>
            <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
    
    <!-- bill Content -->
    <div id="bill-content" class="bg-white rounded-xl shadow-lg p-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8 pb-6 border-b-2 border-gray-200">
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-bill-dollar text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800"><?php echo $company['company_name']; ?></h1>
                        <p class="text-gray-600">bill</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1">
                    <p><?php echo $company['address']; ?></p>
                    <p><i class="fas fa-phone mr-2"></i><?php echo $company['phone']; ?></p>
                    <p><i class="fas fa-envelope mr-2"></i><?php echo $company['email']; ?></p>
                    <?php if ($company['website']): ?>
                        <p><i class="fas fa-globe mr-2"></i><?php echo $company['website']; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="text-right">
                <h2 class="text-4xl font-bold text-blue-600 mb-4"><?php echo $bill['bill_number']; ?></h2>
                <div class="space-y-2 text-sm">
                    <p><strong>bill Date:</strong> <?php echo formatDate($bill['bill_date']); ?></p>
                    <p><strong>Due Date:</strong> <?php echo formatDate($bill['due_date']); ?></p>
                    <p>
                        <strong>Status:</strong> 
                        <span class="badge badge-<?php echo $bill['status'] === 'paid' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($bill['status']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Bill To -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Bill To:</h3>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-semibold text-gray-800 text-lg"><?php echo $bill['customer_name']; ?></p>
                <?php if ($bill['customer_address']): ?>
                    <p class="text-gray-600"><?php echo $bill['customer_address']; ?></p>
                    <p class="text-gray-600">
                        <?php echo implode(', ', array_filter([$bill['city'], $bill['state'], $bill['zip_code']])); ?>
                    </p>
                    <?php if ($bill['country']): ?>
                        <p class="text-gray-600"><?php echo $bill['country']; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($bill['customer_email']): ?>
                    <p class="text-gray-600 mt-2"><i class="fas fa-envelope mr-2"></i><?php echo $bill['customer_email']; ?></p>
                <?php endif; ?>
                <?php if ($bill['customer_phone']): ?>
                    <p class="text-gray-600"><i class="fas fa-phone mr-2"></i><?php echo $bill['customer_phone']; ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Items Table -->
        <div class="mb-8">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Description</th>
                        <th class="px-4 py-3 text-center font-semibold">Quantity</th>
                        <th class="px-4 py-3 text-right font-semibold">Unit Price</th>
                        <th class="px-4 py-3 text-right font-semibold">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="px-4 py-3 text-gray-700"><?php echo $item['description']; ?></td>
                            <td class="px-4 py-3 text-center text-gray-700"><?php echo $item['quantity']; ?></td>
                            <td class="px-4 py-3 text-right text-gray-700"><?php echo formatCurrency($item['unit_price']); ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800"><?php echo formatCurrency($item['amount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="flex justify-end mb-8">
            <div class="w-full md:w-1/2 lg:w-1/3 space-y-3">
                <div class="flex justify-between items-center pb-2">
                    <span class="text-gray-700 font-semibold">Subtotal:</span>
                    <span class="text-gray-800 font-bold"><?php echo formatCurrency($bill['subtotal']); ?></span>
                </div>
                
                <?php if ($bill['tax_rate'] > 0): ?>
                    <div class="flex justify-between items-center pb-2">
                        <span class="text-gray-700 font-semibold">Tax (<?php echo $bill['tax_rate']; ?>%):</span>
                        <span class="text-gray-800 font-bold"><?php echo formatCurrency($bill['tax_amount']); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($bill['discount_amount'] > 0): ?>
                    <div class="flex justify-between items-center pb-2">
                        <span class="text-gray-700 font-semibold">Discount:</span>
                        <span class="text-red-600 font-bold">-<?php echo formatCurrency($bill['discount_amount']); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="flex justify-between items-center pt-3 border-t-2 border-gray-300">
                    <span class="text-xl font-bold text-gray-800">Total:</span>
                    <span class="text-2xl font-bold text-blue-600"><?php echo formatCurrency($bill['total_amount']); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        <?php if ($bill['notes']): ?>
            <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-600">
                <h4 class="font-bold text-gray-800 mb-2">Notes:</h4>
                <p class="text-gray-700"><?php echo nl2br($bill['notes']); ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
            <p>Thank you for your business!</p>
            <?php if ($company['tax_id']): ?>
                <p class="mt-2">Tax ID: <?php echo $company['tax_id']; ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('bill-content');
    const opt = {
        margin: 10,
        filename: '<?php echo $bill['bill_number']; ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>

<?php include '../includes/footer.php'; ?>
