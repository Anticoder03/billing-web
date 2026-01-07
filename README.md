# Billing Web Application

A comprehensive PHP/MySQL billing and invoicing management system with modern UI built using Tailwind CSS.

## Features

- **Dashboard**: Overview of invoices, bills, payments, and pending amounts
- **Invoice Management**: Create, view, edit, and delete invoices with PDF download
- **Bill Management**: Create, view, edit, and delete bills with PDF download
- **Payment Tracking**: Record and manage payments linked to invoices/bills
- **Legal Documents**: Create legal documents with digital signature capture
- **PDF Generation**: Client-side PDF generation using html2pdf.js
- **Digital Signatures**: Signature capture using signature_pad.js
- **Responsive Design**: Mobile-friendly interface with Tailwind CSS
- **Modern UI**: Beautiful gradients, animations, and icons

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/XAMPP web server
- Modern web browser

## Installation

1. **Clone or download** this project to your XAMPP htdocs directory:
   ```
   c:\xampp\htdocs\billing-web\main\
   ```

2. **Import the database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database or use the SQL file
   - Import `database.sql`

3. **Configure database connection**:
   - Open `config/database.php`
   - Update database credentials if needed (default: root with no password)

4. **Start XAMPP**:
   - Start Apache and MySQL services

5. **Access the application**:
   - Open browser and navigate to: `http://localhost/billing-web/main/`

## Default Login Credentials

- **Username**: admin
- **Password**: admin123

## Project Structure

```
main/
├── assets/
│   ├── css/
│   │   └── custom.css
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
├── database.sql
├── dashboard.php
├── index.php (login page)
└── README.md
```

## Usage

### Creating an Invoice

1. Navigate to **Invoices** > **Create Invoice**
2. Select customer and fill in invoice details
3. Add line items (description, quantity, price)
4. Set tax rate and discount if applicable
5. Click **Create Invoice**

### Viewing and Downloading PDFs

1. Go to invoice/bill listing page
2. Click the **View** icon
3. Click **Download PDF** button to save as PDF

### Recording Payments

1. Navigate to **Payments**
2. Click **Record Payment**
3. Select customer and related invoice/bill
4. Enter payment details
5. Submit to record payment

### Creating Legal Documents

1. Navigate to **Legal Documents** > **Create Document**
2. Fill in document details and content
3. Use signature pads to capture company and client signatures
4. Click **Create Document**
5. View and download the signed document as PDF

## Technologies Used

- **Backend**: PHP (native)
- **Database**: MySQL with PDO
- **Frontend**: HTML5, Tailwind CSS (CDN)
- **Icons**: Font Awesome 6
- **PDF Generation**: html2pdf.js
- **Signature Capture**: signature_pad.js
- **JavaScript**: Vanilla JS

## Security Features

- Password hashing using PHP's password_hash()
- PDO prepared statements to prevent SQL injection
- Session-based authentication
- Input sanitization
- CSRF protection ready

## Browser Support

- Chrome (recommended)
- Firefox
- Edge
- Safari

## License

This project is open source and available for personal and commercial use.

## Support

For issues or questions, please refer to the documentation or contact support.
