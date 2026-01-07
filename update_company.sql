-- Update company information to Tech Fellows
UPDATE company_info SET 
    company_name = 'Tech Fellows',
    address = 'Shop No.3 Rahul Apartment Ram Nagar Chhiri Vapi Gujarat 396191',
    phone = '+91 7383745943',
    email = 'official@techfellows.tech',
    website = 'www.techfellows.tech',
    tax_id = 'GST123456'
WHERE id = 1;
