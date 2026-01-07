<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'View Legal Document';

$db = new Database();
$conn = $db->connect();

$document_id = $_GET['id'] ?? 0;

// Get document details
$stmt = $conn->prepare("SELECT ld.*, c.name as customer_name, c.email as customer_email, 
    c.address as customer_address, c.city, c.state, c.zip_code, c.country
    FROM legal_documents ld 
    LEFT JOIN customers c ON ld.customer_id = c.id 
    WHERE ld.id = ?");
$stmt->execute([$document_id]);
$document = $stmt->fetch();

if (!$document) {
    header('Location: index.php');
    exit();
}

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
            <i class="fas fa-file-contract text-cyan-600 mr-2"></i>
            Legal Document
        </h2>
        <div class="flex space-x-3">
            <button onclick="downloadPDF()" 
                    class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-download mr-2"></i>Download PDF
            </button>
            <a href="delete.php?id=<?php echo $document['id']; ?>" 
               onclick="return confirmDelete('Are you sure you want to delete this document?')"
               class="bg-gradient-to-r from-red-600 to-red-700 text-white px-6 py-3 rounded-lg font-semibold hover:from-red-700 hover:to-red-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-trash mr-2"></i>Delete
            </a>
            <a href="index.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>
    
    <!-- Document Content -->
    <div id="document-content" class="bg-white rounded-xl shadow-lg p-12">
        <!-- Header -->
        <div class="text-center mb-8 pb-6 border-b-2 border-gray-200">
            <div class="flex items-center justify-center space-x-3 mb-4">
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-600 to-cyan-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-contract text-3xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo $company['company_name']; ?></h1>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-cyan-600 mt-4"><?php echo $document['title']; ?></h2>
            <p class="text-gray-600 mt-2"><?php echo $document['document_type']; ?></p>
        </div>
        
        <!-- Document Info -->
        <div class="grid grid-cols-2 gap-6 mb-8 p-4 bg-gray-50 rounded-lg">
            <div>
                <p class="text-sm font-semibold text-gray-600">Document Number:</p>
                <p class="text-lg font-bold text-gray-800"><?php echo $document['document_number']; ?></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-600">Document Date:</p>
                <p class="text-lg font-bold text-gray-800"><?php echo formatDate($document['document_date']); ?></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-600">Client:</p>
                <p class="text-lg font-bold text-gray-800"><?php echo $document['customer_name']; ?></p>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-600">Status:</p>
                <span class="badge badge-<?php echo $document['status'] === 'signed' ? 'success' : 'secondary'; ?>">
                    <?php echo ucfirst($document['status']); ?>
                </span>
            </div>
        </div>
        
        <!-- Document Content -->
        <div class="mb-8 p-6 bg-white border border-gray-200 rounded-lg">
            <div class="prose max-w-none text-gray-700 leading-relaxed whitespace-pre-wrap">
                <?php echo nl2br(htmlspecialchars($document['content'])); ?>
            </div>
        </div>
        
        <!-- Signatures -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12 pt-8 border-t-2 border-gray-200">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Company Signature</h3>
                <?php if ($document['company_signature']): ?>
                    <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
                        <img src="<?php echo $document['company_signature']; ?>" alt="Company Signature" class="max-w-full h-32">
                    </div>
                    <div class="mt-3">
                        <p class="font-semibold text-gray-800"><?php echo $company['company_name']; ?></p>
                        <p class="text-sm text-gray-600">Authorized Signatory</p>
                    </div>
                <?php else: ?>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400">
                        <i class="fas fa-signature text-3xl mb-2"></i>
                        <p>Not signed</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Client Signature</h3>
                <?php if ($document['client_signature']): ?>
                    <div class="border-2 border-gray-300 rounded-lg p-4 bg-white">
                        <img src="<?php echo $document['client_signature']; ?>" alt="Client Signature" class="max-w-full h-32">
                    </div>
                    <div class="mt-3">
                        <p class="font-semibold text-gray-800"><?php echo $document['customer_name']; ?></p>
                        <p class="text-sm text-gray-600">Client</p>
                    </div>
                <?php else: ?>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-400">
                        <i class="fas fa-signature text-3xl mb-2"></i>
                        <p>Not signed</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center text-sm text-gray-600">
            <p><?php echo $company['company_name']; ?></p>
            <p><?php echo $company['address']; ?></p>
            <p><?php echo $company['phone']; ?> | <?php echo $company['email']; ?></p>
        </div>
    </div>
</div>

<script>
function downloadPDF() {
    const element = document.getElementById('document-content');
    const opt = {
        margin: 10,
        filename: '<?php echo $document['document_number']; ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    
    html2pdf().set(opt).from(element).save();
}
</script>

<?php include '../includes/footer.php'; ?>
