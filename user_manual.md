# Billing Web Application - User Manual

## Table of Contents

1. [Introduction](#introduction)
2. [System Requirements](#system-requirements)
3. [Installation Guide](#installation-guide)
4. [Getting Started](#getting-started)
5. [Dashboard Overview](#dashboard-overview)
6. [Customer Management](#customer-management)
7. [Invoice Management](#invoice-management)
8. [Bill Management](#bill-management)
9. [Payment Management](#payment-management)
10. [Legal Documents](#legal-documents)
11. [Troubleshooting](#troubleshooting)
12. [Security Best Practices](#security-best-practices)

---

## Introduction

The Billing Web Application is a comprehensive PHP/MySQL-based invoicing and billing management system designed to help businesses manage their financial operations efficiently. The application features a modern, responsive interface built with Tailwind CSS and includes powerful features such as:

- Invoice and bill creation with PDF generation
- Payment tracking and management
- Customer relationship management
- Legal document creation with digital signatures
- Real-time dashboard analytics
- Mobile-friendly responsive design

---

## System Requirements

### Server Requirements
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Web Server**: Apache (XAMPP recommended for local development)
- **Storage**: Minimum 100MB free space

### Browser Requirements
- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari

### Internet Connection
- Required for CDN resources (Tailwind CSS, Font Awesome, html2pdf.js, signature_pad.js)

---

## Installation Guide

### Step 1: Download and Setup XAMPP

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install XAMPP on your computer
3. Start the XAMPP Control Panel

### Step 2: Deploy Application Files

1. Copy the application folder to your XAMPP htdocs directory:
   ```
   c:\xampp\htdocs\billing-web\main\
   ```

### Step 3: Database Setup

1. Start Apache and MySQL services from XAMPP Control Panel
2. Open your web browser and navigate to: `http://localhost/phpmyadmin`
3. Click on "New" to create a new database or use the SQL tab
4. Import the database schema:
   - Click on the "Import" tab
   - Choose the `database.sql` file from the application folder
   - Click "Go" to execute the import

The database will be created with the name `billing_system_main` and will include:
- Default admin user
- Sample company information (Tech Fellows)
- Sample customer records

### Step 4: Configure Database Connection

1. Navigate to `config/database.php`
2. Verify the database credentials (default settings):
   ```php
   $host = 'localhost';
   $dbname = 'billing_system_main';
   $username = 'root';
   $password = '';
   ```
3. Update credentials if your MySQL setup differs

### Step 5: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/billing-web/main/`
3. You should see the login page

---

## Getting Started

### First Login

**Default Credentials:**
- **Username**: `admin`
- **Password**: `admin123`

> **⚠️ IMPORTANT**: Change the default password immediately after first login for security purposes.

### Login Process

1. Navigate to `http://localhost/billing-web/main/`
2. Enter your username and password
3. Click "Login" button
4. Upon successful authentication, you'll be redirected to the Dashboard

### Logout

1. Click on the logout icon in the sidebar
2. You'll be redirected to the login page
3. Your session will be terminated securely

---

## Dashboard Overview

The Dashboard is your central hub for monitoring business activities. It provides:

### Key Metrics Cards

1. **Total Invoices**: Displays the total number of invoices created
2. **Total Bills**: Shows the count of all bills in the system
3. **Total Payments**: Displays the total number of payment records
4. **Pending Amount**: Shows the sum of all unpaid invoices and bills

### Recent Activity

- **Recent Invoices**: Lists the 5 most recent invoices with status indicators
- **Recent Bills**: Shows the 5 most recent bills
- **Recent Payments**: Displays the latest payment transactions

### Navigation

The sidebar provides quick access to all modules:
- Dashboard
- Customers
- Invoices
- Bills
- Payments
- Legal Documents
- Logout

---

## Customer Management

### Viewing Customers

1. Click on **Customers** in the sidebar
2. View the list of all customers with their details:
   - Name
   - Email
   - Phone
   - Address
   - City, State, ZIP Code
   - Country

### Adding a New Customer

1. Navigate to **Customers** → **Create Customer**
2. Fill in the customer information form:
   - **Name** (required)
   - **Email** (optional but recommended)
   - **Phone** (optional)
   - **Address** (optional)
   - **City** (optional)
   - **State** (optional)
   - **ZIP Code** (optional)
   - **Country** (optional)
   - **Tax ID** (optional)
3. Click **Create Customer** button
4. You'll be redirected to the customer list with a success message

### Editing Customer Information

1. Go to the Customers page
2. Click the **Edit** icon (pencil) next to the customer you want to modify
3. Update the necessary fields
4. Click **Update Customer** to save changes

### Deleting a Customer

1. Navigate to the Customers page
2. Click the **Delete** icon (trash) next to the customer
3. Confirm the deletion when prompted

> **⚠️ WARNING**: Deleting a customer will also delete all associated invoices, bills, payments, and legal documents due to cascade deletion.

---

## Invoice Management

### Viewing Invoices

1. Click on **Invoices** in the sidebar
2. View all invoices with the following information:
   - Invoice Number
   - Customer Name
   - Invoice Date
   - Due Date
   - Total Amount
   - Status (Draft, Sent, Paid, Overdue, Cancelled)
   - Actions (View, Edit, Delete)

### Creating a New Invoice

1. Navigate to **Invoices** → **Create Invoice**
2. Fill in the invoice header information:
   - **Invoice Number** (auto-generated or custom)
   - **Customer** (select from dropdown)
   - **Invoice Date** (required)
   - **Due Date** (optional)
   - **Status** (Draft, Sent, Paid, Overdue, Cancelled)

3. Add invoice line items:
   - Click **Add Item** button
   - Enter **Description** of the product/service
   - Enter **Quantity**
   - Enter **Unit Price**
   - The **Amount** will be calculated automatically (Quantity × Unit Price)
   - Repeat for multiple items

4. Set financial details:
   - **Tax Rate** (percentage, e.g., 18 for 18%)
   - **Discount Amount** (fixed amount)
   - The system will automatically calculate:
     - Subtotal (sum of all line items)
     - Tax Amount (subtotal × tax rate)
     - Total Amount (subtotal + tax - discount)

5. Add **Notes** (optional) for additional information
6. Click **Create Invoice** button

### Viewing Invoice Details

1. From the Invoices list, click the **View** icon (eye)
2. The invoice detail page displays:
   - Complete invoice information
   - Company details (from company_info table)
   - Customer details
   - All line items
   - Financial breakdown
   - Status and notes

### Downloading Invoice as PDF

1. Open the invoice detail page (View)
2. Click the **Download PDF** button
3. The PDF will be generated using html2pdf.js library
4. The PDF includes:
   - Company logo and information
   - Invoice number and dates
   - Customer information
   - Itemized list of products/services
   - Tax and discount calculations
   - Total amount
   - Notes

### Editing an Invoice

1. From the Invoices list, click the **Edit** icon (pencil)
2. Modify any invoice details:
   - Header information
   - Line items (add, edit, or remove)
   - Tax rate and discount
   - Status
   - Notes
3. Click **Update Invoice** to save changes

### Deleting an Invoice

1. From the Invoices list, click the **Delete** icon (trash)
2. Confirm the deletion
3. The invoice and all associated line items will be permanently removed

> **📝 NOTE**: Deleting an invoice will set any associated payments' invoice_id to NULL rather than deleting the payment records.

---

## Bill Management

Bill management works identically to Invoice management but is used for tracking expenses or vendor bills.

### Viewing Bills

1. Click on **Bills** in the sidebar
2. View all bills with:
   - Bill Number
   - Customer/Vendor Name
   - Bill Date
   - Due Date
   - Total Amount
   - Status
   - Actions

### Creating a New Bill

1. Navigate to **Bills** → **Create Bill**
2. Follow the same process as creating an invoice:
   - Fill in bill header (Bill Number, Customer, Dates, Status)
   - Add line items (Description, Quantity, Unit Price)
   - Set tax rate and discount
   - Add notes
3. Click **Create Bill**

### Viewing and Downloading Bill PDFs

1. Click the **View** icon on any bill
2. Review bill details
3. Click **Download PDF** to generate and save the bill as a PDF document

### Editing and Deleting Bills

- **Edit**: Click the pencil icon, modify details, and save
- **Delete**: Click the trash icon and confirm deletion

---

## Payment Management

### Viewing Payments

1. Click on **Payments** in the sidebar
2. View all payment records with:
   - Payment Number
   - Customer Name
   - Related Invoice/Bill
   - Payment Date
   - Amount
   - Payment Method
   - Reference Number

### Recording a New Payment

1. Navigate to **Payments** → **Record Payment**
2. Fill in the payment form:
   - **Payment Number** (auto-generated or custom)
   - **Customer** (select from dropdown)
   - **Related Invoice** (optional - select if payment is for a specific invoice)
   - **Related Bill** (optional - select if payment is for a specific bill)
   - **Payment Date** (required)
   - **Amount** (required)
   - **Payment Method** (Cash, Check, Credit Card, Bank Transfer, Other)
   - **Reference Number** (optional - check number, transaction ID, etc.)
   - **Notes** (optional)
3. Click **Record Payment**

### Payment Methods

The system supports the following payment methods:
- **Cash**: Direct cash payments
- **Check**: Payment by check (use Reference Number for check number)
- **Credit Card**: Credit/debit card payments
- **Bank Transfer**: Wire transfers or ACH payments
- **Other**: Any other payment method

### Deleting a Payment

1. From the Payments list, click the **Delete** icon
2. Confirm the deletion
3. The payment record will be permanently removed

> **📝 NOTE**: Deleting a payment does not affect the associated invoice or bill.

---

## Legal Documents

The Legal Documents module allows you to create, sign, and manage legal agreements with clients.

### Viewing Legal Documents

1. Click on **Legal Documents** in the sidebar
2. View all documents with:
   - Document Number
   - Customer Name
   - Document Type
   - Title
   - Document Date
   - Status (Draft, Signed, Cancelled)
   - Actions

### Creating a Legal Document

1. Navigate to **Legal Documents** → **Create Document**
2. Fill in the document information:
   - **Document Number** (auto-generated or custom)
   - **Customer** (select from dropdown)
   - **Document Type** (e.g., "Service Agreement", "Contract", "NDA")
   - **Title** (e.g., "Website Development Agreement")
   - **Document Date** (required)
   - **Content** (the full text of the legal document)
   - **Status** (Draft, Signed, Cancelled)

3. **Digital Signatures**:
   - **Company Signature**: Draw your company's signature in the signature pad
   - **Client Signature**: Have the client draw their signature in the signature pad
   - Use the **Clear** button to reset a signature if needed

4. Click **Create Document**

### Viewing Legal Documents

1. Click the **View** icon on any legal document
2. The document view displays:
   - Document header information
   - Full document content
   - Company signature (if captured)
   - Client signature (if captured)
   - Document status

### Downloading Legal Documents as PDF

1. Open the document detail page
2. Click **Download PDF**
3. The PDF will include:
   - Document information
   - Full content
   - Both signatures (as images)
   - Date and status

### Digital Signature Feature

The application uses the `signature_pad.js` library for capturing signatures:
- Draw signatures using mouse (desktop) or touch (mobile)
- Signatures are saved as base64-encoded images
- Signatures are embedded in PDFs when downloaded
- Clear and redraw signatures as needed

### Deleting Legal Documents

1. From the Legal Documents list, click the **Delete** icon
2. Confirm the deletion
3. The document will be permanently removed

---

## Troubleshooting

### Common Issues and Solutions

#### Cannot Login / "Invalid Credentials" Error

**Solution:**
1. Verify you're using the correct default credentials:
   - Username: `admin`
   - Password: `admin123`
2. Check that the database was imported correctly
3. Verify the `users` table exists and contains the admin user

#### Database Connection Error

**Solution:**
1. Ensure MySQL service is running in XAMPP
2. Check `config/database.php` for correct credentials
3. Verify the database `billing_system_main` exists
4. Test database connection in phpMyAdmin

#### PDF Download Not Working

**Solution:**
1. Check browser console for JavaScript errors
2. Ensure you have an active internet connection (required for CDN libraries)
3. Try a different browser (Chrome recommended)
4. Disable browser extensions that might block downloads

#### Signature Pad Not Working

**Solution:**
1. Verify internet connection (signature_pad.js is loaded from CDN)
2. Check browser console for errors
3. Try using a different browser
4. Ensure JavaScript is enabled in your browser

#### Images/Styles Not Loading

**Solution:**
1. Check that Apache is running in XAMPP
2. Verify the `.htaccess` file is present in the main directory
3. Ensure `mod_rewrite` is enabled in Apache
4. Check browser console for 404 errors
5. Verify internet connection for CDN resources (Tailwind CSS, Font Awesome)

#### "Page Not Found" Errors

**Solution:**
1. Check that you're accessing the correct URL: `http://localhost/billing-web/main/`
2. Verify Apache is running
3. Check that all PHP files are in the correct directories
4. Review Apache error logs in XAMPP

#### Session Timeout / Automatic Logout

**Solution:**
1. This is normal behavior after extended inactivity
2. Simply log in again
3. To extend session timeout, modify `session.gc_maxlifetime` in `php.ini`

---

## Security Best Practices

### Password Management

1. **Change Default Password**: Immediately change the default admin password after installation
2. **Use Strong Passwords**: Combine uppercase, lowercase, numbers, and special characters
3. **Regular Updates**: Change passwords periodically

### Database Security

1. **Secure Credentials**: Never share database credentials
2. **Backup Regularly**: Create regular database backups
3. **Restrict Access**: Limit database access to authorized users only

### Application Security

1. **Keep Updated**: Regularly update PHP and MySQL to the latest stable versions
2. **HTTPS**: Use HTTPS in production environments
3. **File Permissions**: Set appropriate file permissions (read-only for most files)
4. **Regular Backups**: Backup both database and application files

### User Access

1. **Limit Admin Access**: Create separate user accounts for different team members (requires customization)
2. **Monitor Activity**: Regularly review created invoices, bills, and payments
3. **Logout**: Always logout when finished, especially on shared computers

### Data Privacy

1. **Customer Data**: Handle customer information responsibly
2. **Legal Compliance**: Ensure compliance with data protection regulations (GDPR, etc.)
3. **Secure Storage**: Keep backups in secure, encrypted locations

---

## Additional Features

### Company Information

The application uses company information stored in the `company_info` table for:
- PDF headers and footers
- Invoice and bill branding
- Legal document headers

**Default Company Information:**
- **Name**: Tech Fellows
- **Address**: Shop No.3 Rahul Apartment Ram Nagar Chhiri Vapi Gujarat 396191
- **Phone**: +91 7383745943
- **Email**: official@techfellows.tech
- **Website**: www.techfellows.tech

To update company information, modify the `company_info` table directly in phpMyAdmin or create an admin interface (requires customization).

### Status Management

**Invoice/Bill Statuses:**
- **Draft**: Initial state, not yet sent to customer
- **Sent**: Invoice/bill has been sent to customer
- **Paid**: Payment has been received
- **Overdue**: Past due date and not paid
- **Cancelled**: Invoice/bill has been cancelled

**Legal Document Statuses:**
- **Draft**: Document is being prepared
- **Signed**: Both parties have signed
- **Cancelled**: Document has been cancelled

### Automatic Calculations

The system automatically calculates:
- **Line Item Amount**: Quantity × Unit Price
- **Subtotal**: Sum of all line item amounts
- **Tax Amount**: Subtotal × Tax Rate
- **Total Amount**: Subtotal + Tax Amount - Discount Amount

---

## Technical Information

### Technologies Used

- **Backend**: PHP (native, no framework)
- **Database**: MySQL with PDO for secure queries
- **Frontend**: HTML5, Tailwind CSS (via CDN)
- **Icons**: Font Awesome 6
- **PDF Generation**: html2pdf.js (client-side)
- **Signature Capture**: signature_pad.js
- **JavaScript**: Vanilla JavaScript (no jQuery)

### Database Tables

- `users`: User authentication
- `company_info`: Company details
- `customers`: Customer/client information
- `invoices`: Invoice headers
- `invoice_items`: Invoice line items
- `bills`: Bill headers
- `bill_items`: Bill line items
- `payments`: Payment records
- `legal_documents`: Legal agreements

### File Structure

```
main/
├── assets/
│   ├── css/custom.css
│   └── js/
│       ├── main.js
│       ├── pdf-generator.js
│       └── signature.js
├── auth/
│   └── logout.php
├── bills/
│   ├── index.php
│   ├── create.php
│   ├── view.php
│   ├── edit.php
│   └── delete.php
├── config/
│   ├── config.php
│   └── database.php
├── customers/
│   ├── index.php
│   ├── create.php
│   ├── edit.php
│   └── delete.php
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   ├── footer.php
│   └── functions.php
├── invoices/
│   ├── index.php
│   ├── create.php
│   ├── view.php
│   ├── edit.php
│   └── delete.php
├── legal/
│   ├── index.php
│   ├── create.php
│   ├── view.php
│   └── delete.php
├── payments/
│   ├── index.php
│   ├── create.php
│   └── delete.php
├── dashboard.php
├── index.php (login)
└── database.sql
```

---

## Support and Maintenance

### Regular Maintenance Tasks

1. **Database Backup**: Weekly backups recommended
2. **Clear Old Data**: Archive or delete old records periodically
3. **Monitor Disk Space**: Ensure adequate storage
4. **Update Software**: Keep PHP and MySQL updated

### Getting Help

For technical issues or questions:
1. Review this user manual
2. Check the troubleshooting section
3. Review application logs
4. Contact your system administrator or developer

---

## Appendix

### Keyboard Shortcuts

- **Tab**: Navigate between form fields
- **Enter**: Submit forms
- **Esc**: Close modals (if implemented)

### Browser Recommendations

For best performance and compatibility:
1. **Primary**: Google Chrome (latest version)
2. **Alternative**: Mozilla Firefox (latest version)
3. **Alternative**: Microsoft Edge (latest version)

### Mobile Usage

The application is fully responsive and works on mobile devices:
- Touch-friendly interface
- Responsive tables
- Mobile-optimized forms
- Touch signature capture

---

## Glossary

- **Invoice**: A document sent to a customer requesting payment for goods/services
- **Bill**: A document received from a vendor requesting payment (or used interchangeably with invoice)
- **Customer**: A client or business entity that receives invoices/bills
- **Line Item**: Individual product or service entry in an invoice/bill
- **Subtotal**: Sum of all line items before tax and discount
- **Tax Rate**: Percentage of tax applied to the subtotal
- **Discount**: Amount deducted from the total
- **Payment**: A record of money received from a customer
- **Legal Document**: A formal agreement between company and client
- **Digital Signature**: Electronic signature captured using signature pad
- **PDF**: Portable Document Format - universal file format for documents
- **Status**: Current state of an invoice, bill, or document

---

**Document Version**: 1.0  
**Last Updated**: January 2026  
**Application Version**: 1.0

---

*This user manual is subject to updates as the application evolves. Please refer to the latest version for accurate information.*
