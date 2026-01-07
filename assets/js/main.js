// Main JavaScript file

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const isClickInsideSidebar = sidebar.contains(event.target);
        const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
        
        if (!isClickInsideSidebar && !isClickOnToggle && window.innerWidth < 1024) {
            sidebar.classList.add('-translate-x-full');
        }
    });
    
    // Set active navigation item
    const currentPath = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        if (item.getAttribute('href') && currentPath.includes(item.getAttribute('href'))) {
            item.classList.add('active');
        }
    });
});

// Auto-hide flash messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// Confirm delete actions
function confirmDelete(message = 'Are you sure you want to delete this item?') {
    return confirm(message);
}

// Format currency input
function formatCurrencyInput(input) {
    let value = input.value.replace(/[^\d.]/g, '');
    const parts = value.split('.');
    if (parts.length > 2) {
        value = parts[0] + '.' + parts.slice(1).join('');
    }
    if (parts[1] && parts[1].length > 2) {
        value = parts[0] + '.' + parts[1].substring(0, 2);
    }
    input.value = value;
}

// Calculate line item amount
function calculateLineAmount(quantity, price) {
    return (parseFloat(quantity) || 0) * (parseFloat(price) || 0);
}

// Calculate invoice/bill totals
function calculateTotals() {
    let subtotal = 0;
    
    // Calculate subtotal from all line items
    document.querySelectorAll('.line-item').forEach(item => {
        const amount = parseFloat(item.querySelector('.item-amount').textContent.replace(/[^\d.]/g, '')) || 0;
        subtotal += amount;
    });
    
    // Get tax rate and discount
    const taxRate = parseFloat(document.getElementById('tax_rate')?.value) || 0;
    const discount = parseFloat(document.getElementById('discount')?.value) || 0;
    
    // Calculate tax and total
    const taxAmount = (subtotal * taxRate) / 100;
    const total = subtotal + taxAmount - discount;
    
    // Update display
    if (document.getElementById('subtotal')) {
        document.getElementById('subtotal').textContent = '₹' + subtotal.toFixed(2);
    }
    if (document.getElementById('tax_amount')) {
        document.getElementById('tax_amount').textContent = '₹' + taxAmount.toFixed(2);
    }
    if (document.getElementById('total')) {
        document.getElementById('total').textContent = '₹' + total.toFixed(2);
    }
    
    // Update hidden inputs
    if (document.getElementById('subtotal_input')) {
        document.getElementById('subtotal_input').value = subtotal.toFixed(2);
    }
    if (document.getElementById('tax_amount_input')) {
        document.getElementById('tax_amount_input').value = taxAmount.toFixed(2);
    }
    if (document.getElementById('total_input')) {
        document.getElementById('total_input').value = total.toFixed(2);
    }
}

// Add new line item (for invoice/bill creation)
function addLineItem() {
    const container = document.getElementById('line-items-container');
    const itemCount = container.children.length + 1;
    
    const itemHTML = `
        <div class="line-item grid grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
            <div class="col-span-12 md:col-span-5">
                <input type="text" name="items[${itemCount}][description]" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                       placeholder="Item description" required>
            </div>
            <div class="col-span-4 md:col-span-2">
                <input type="number" name="items[${itemCount}][quantity]" 
                       class="item-quantity w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                       placeholder="Qty" step="0.01" value="1" required onchange="updateLineItem(this)">
            </div>
            <div class="col-span-4 md:col-span-2">
                <input type="number" name="items[${itemCount}][price]" 
                       class="item-price w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" 
                       placeholder="Price" step="0.01" required onchange="updateLineItem(this)">
            </div>
            <div class="col-span-3 md:col-span-2 flex items-center">
                <span class="item-amount font-semibold text-gray-700">₹0.00</span>
            </div>
            <div class="col-span-1 md:col-span-1 flex items-center">
                <button type="button" onclick="removeLineItem(this)" 
                        class="text-red-600 hover:text-red-800 transition-colors">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', itemHTML);
}

// Remove line item
function removeLineItem(button) {
    const item = button.closest('.line-item');
    item.remove();
    calculateTotals();
}

// Update line item amount
function updateLineItem(input) {
    const lineItem = input.closest('.line-item');
    const quantity = parseFloat(lineItem.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(lineItem.querySelector('.item-price').value) || 0;
    const amount = quantity * price;
    
    lineItem.querySelector('.item-amount').textContent = '₹' + amount.toFixed(2);
    calculateTotals();
}

// Print function
function printPage() {
    window.print();
}
