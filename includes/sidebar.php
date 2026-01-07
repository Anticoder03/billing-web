<!-- Sidebar Navigation -->
<aside id="sidebar" class="fixed top-0 left-0 h-screen w-64 bg-gradient-to-b from-darker to-dark text-white shadow-2xl transform transition-transform duration-300 ease-in-out z-50">
    <!-- Logo & Company Name -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">Billing Pro</h1>
                <p class="text-xs text-gray-400">Management System</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="p-4 space-y-2 overflow-y-auto h-[calc(100vh-180px)]">
        <!-- Dashboard -->
        <a href="<?php echo BASE_URL; ?>dashboard.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
            <i class="fas fa-home text-lg group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <!-- Invoices -->
        <div class="space-y-1">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Invoices</div>
            <a href="<?php echo BASE_URL; ?>invoices/create.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-plus-circle text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Create Invoice</span>
            </a>
            <a href="<?php echo BASE_URL; ?>invoices/index.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-file-invoice text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">View All Invoices</span>
            </a>
        </div>
        
        <!-- Bills -->
        <div class="space-y-1">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bills</div>
            <a href="<?php echo BASE_URL; ?>bills/create.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-plus-circle text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Create Bill</span>
            </a>
            <a href="<?php echo BASE_URL; ?>bills/index.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-receipt text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">View All Bills</span>
            </a>
        </div>
        
        <!-- Customers -->
        <div class="space-y-1">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customers</div>
            <a href="<?php echo BASE_URL; ?>customers/create.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-user-plus text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Add Customer</span>
            </a>
            <a href="<?php echo BASE_URL; ?>customers/index.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-users text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">View All Customers</span>
            </a>
        </div>
        
        <!-- Payments -->
        <a href="<?php echo BASE_URL; ?>payments/index.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
            <i class="fas fa-money-bill-wave text-lg group-hover:scale-110 transition-transform"></i>
            <span class="font-medium">Payments</span>
        </a>
        
        <!-- Legal Documents -->
        <div class="space-y-1">
            <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Legal</div>
            <a href="<?php echo BASE_URL; ?>legal/create.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-file-contract text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">Create Document</span>
            </a>
            <a href="<?php echo BASE_URL; ?>legal/index.php" class="nav-item flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-white/10 transition-all duration-200 group">
                <i class="fas fa-folder-open text-lg group-hover:scale-110 transition-transform"></i>
                <span class="font-medium">View Documents</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Mobile Menu Toggle -->
<button id="sidebarToggle" class="fixed top-4 left-4 z-50 lg:hidden bg-darker text-white p-3 rounded-lg shadow-lg">
    <i class="fas fa-bars text-xl"></i>
</button>

<!-- Main Content Wrapper -->
<div class="lg:ml-64 min-h-screen">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="px-6 py-4 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h2>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-700"><?php echo $_SESSION['full_name'] ?? 'User'; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $_SESSION['email'] ?? ''; ?></p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold">
                    <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages -->
    <?php
    $flash = getFlashMessage();
    if ($flash):
        $bgColor = $flash['type'] === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 'bg-red-100 border-red-500 text-red-700';
    ?>
    <div class="mx-6 mt-4">
        <div class="<?php echo $bgColor; ?> border-l-4 p-4 rounded-lg shadow-sm" role="alert">
            <p class="font-medium"><?php echo $flash['message']; ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Page Content -->
    <main class="p-6">
