<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Create Legal Document';

$db = new Database();
$conn = $db->connect();

// Get all customers
$customers = $conn->query("SELECT * FROM customers ORDER BY name")->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $customer_id = $_POST['customer_id'];
        $document_type = sanitize($_POST['document_type']);
        $title = sanitize($_POST['title']);
        $content = $_POST['content'];
        $document_date = $_POST['document_date'];
        $company_signature = $_POST['company_signature'] ?? '';
        $client_signature = $_POST['client_signature'] ?? '';
        $status = !empty($company_signature) && !empty($client_signature) ? 'signed' : 'draft';
        
        // Generate document number
        $document_number = getNextDocumentNumber($conn);
        
        // Insert document
        $stmt = $conn->prepare("INSERT INTO legal_documents (document_number, customer_id, document_type, 
            title, content, company_signature, client_signature, document_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $document_number,
            $customer_id,
            $document_type,
            $title,
            $content,
            $company_signature,
            $client_signature,
            $document_date,
            $status
        ]);
        
        $document_id = $conn->lastInsertId();
        
        setFlashMessage('success', 'Legal document created successfully!');
        header('Location: view.php?id=' . $document_id);
        exit();
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error creating document: ' . $e->getMessage());
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-plus-circle text-cyan-600 mr-2"></i>
            Create Legal Document
        </h2>
        <a href="index.php" class="text-cyan-600 hover:text-cyan-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Documents
        </a>
    </div>
    
    <form method="POST" action="" id="document-form" class="bg-white rounded-xl shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="customer_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-cyan-600 mr-2"></i>Customer/Client *
                </label>
                <select name="customer_id" id="customer_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    <option value="">Select Customer</option>
                    <?php foreach ($customers as $customer): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="document_type" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-tag text-cyan-600 mr-2"></i>Document Type *
                </label>
                <input type="text" name="document_type" id="document_type" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                       placeholder="e.g., Service Agreement, NDA, Contract">
            </div>
            
            <div class="md:col-span-2">
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-heading text-cyan-600 mr-2"></i>Document Title *
                </label>
                <input type="text" name="title" id="title" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                       placeholder="Enter document title">
            </div>
            
            <div>
                <label for="document_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-calendar text-cyan-600 mr-2"></i>Document Date *
                </label>
                <input type="date" name="document_date" id="document_date" required
                       value="<?php echo date('Y-m-d'); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="mb-6">
            <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-file-alt text-cyan-600 mr-2"></i>Document Content *
            </label>
            <textarea name="content" id="content" rows="20" required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent font-mono text-sm"
                      placeholder="Enter the legal document content here...">WEBSITE / SOFTWARE SERVICE AGREEMENT

This Agreement is made on ___ / ___ / 20__, between:

Service Provider:
Name: Tech Fellows
Address: Shop No.3 Rahul Apartment Ram Nagar Chhiri Vapi Gujarat 396191

Client:
Name: __________________________
Address: _______________________

Both parties agree to the following terms:

1. Purpose

The Client engaged the Service Provider for website and/or software development services. An amount has already been paid by the Client and has not been refunded. This Agreement confirms how the paid amount will be adjusted and what will be delivered.

2. Services & Deliverables

The Service Provider agrees to deliver the following:

• Website / Software development
• Admin panel (if applicable)
• Database and basic functionality
• Source code (if included in the original discussion)
• Testing and basic support

Any additional features will be mutually agreed in writing.

3. Payment Understanding

Total Project Amount: ₹ __________
Amount Paid: ₹ __________

The above paid amount shall be adjusted against the final delivery of the website/software and shall not be refunded, unless otherwise mutually agreed in writing.

4. Delivery Timeline

The Service Provider agrees to deliver the completed project within ___ days from the date of this Agreement, subject to timely inputs and approvals from the Client.

5. Ownership

Upon full payment and project completion, all rights related to the website/software will belong to the Client. Until then, the Service Provider retains ownership.

6. Confidentiality

Both parties agree to keep all project-related information confidential and not share it with any third party without consent.

7. Governing Law

This Agreement shall be governed by the laws of India, and any dispute shall be subject to the jurisdiction of __________ (City/State).

8. Mutual Understanding

This Agreement is made with mutual consent, in good faith, and replaces any prior verbal understanding.


Accepted and Agreed

Service Provider
Signature: ____________________
Name: _______________________
Date: _______________________

Client
Signature: ____________________
Name: _______________________
Date: _______________________</textarea>
        </div>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Note:</strong> Signatures will be added manually after printing the document. The signature lines are included in the template above.
            </p>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-cyan-600 to-cyan-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-cyan-700 hover:to-cyan-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Create Document
            </button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
