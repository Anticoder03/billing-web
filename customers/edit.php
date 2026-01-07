<?php
require_once '../config/config.php';
requireLogin();

$pageTitle = 'Edit Customer';

$db = new Database();
$conn = $db->connect();

$customer_id = $_GET['id'] ?? 0;

// Get customer details
$stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch();

if (!$customer) {
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        $city = sanitize($_POST['city']);
        $state = sanitize($_POST['state']);
        $zip_code = sanitize($_POST['zip_code']);
        $country = sanitize($_POST['country']);
        $tax_id = sanitize($_POST['tax_id']);
        
        // Update customer
        $stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, address = ?, 
            city = ?, state = ?, zip_code = ?, country = ?, tax_id = ? WHERE id = ?");
        
        $stmt->execute([
            $name,
            $email,
            $phone,
            $address,
            $city,
            $state,
            $zip_code,
            $country,
            $tax_id,
            $customer_id
        ]);
        
        setFlashMessage('success', 'Customer updated successfully!');
        header('Location: index.php');
        exit();
        
    } catch (Exception $e) {
        setFlashMessage('error', 'Error updating customer: ' . $e->getMessage());
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-edit text-indigo-600 mr-2"></i>
            Edit Customer
        </h2>
        <a href="index.php" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>
    
    <form method="POST" action="" class="bg-white rounded-xl shadow-lg p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="md:col-span-2">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-user text-indigo-600 mr-2"></i>Customer Name *
                </label>
                <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($customer['name']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-envelope text-indigo-600 mr-2"></i>Email
                </label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($customer['email']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-phone text-indigo-600 mr-2"></i>Phone
                </label>
                <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="md:col-span-2">
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>Address
                </label>
                <textarea name="address" id="address" rows="2"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"><?php echo htmlspecialchars($customer['address']); ?></textarea>
            </div>
            
            <div>
                <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-city text-indigo-600 mr-2"></i>City
                </label>
                <input type="text" name="city" id="city" value="<?php echo htmlspecialchars($customer['city']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-map text-indigo-600 mr-2"></i>State/Province
                </label>
                <input type="text" name="state" id="state" value="<?php echo htmlspecialchars($customer['state']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="zip_code" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-mail-bulk text-indigo-600 mr-2"></i>ZIP/Postal Code
                </label>
                <input type="text" name="zip_code" id="zip_code" value="<?php echo htmlspecialchars($customer['zip_code']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-globe text-indigo-600 mr-2"></i>Country
                </label>
                <input type="text" name="country" id="country" value="<?php echo htmlspecialchars($customer['country']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
            
            <div class="md:col-span-2">
                <label for="tax_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-id-card text-indigo-600 mr-2"></i>Tax ID / GST Number
                </label>
                <input type="text" name="tax_id" id="tax_id" value="<?php echo htmlspecialchars($customer['tax_id']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php" class="px-6 py-3 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white px-8 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-indigo-800 transform hover:scale-105 transition-all duration-200 shadow-lg">
                <i class="fas fa-save mr-2"></i>Update Customer
            </button>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
