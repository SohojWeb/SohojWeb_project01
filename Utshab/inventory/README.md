# Raw PHP Inventory App

## Features
- Admin can add products and update price any time.
- Customer can register/login separately.
- Customer can view products, add to cart, checkout.
- Customer has wallet balance and can add balance ("from other app").
- On checkout, total amount is deducted from customer main balance.

## Setup (XAMPP + phpMyAdmin)
1. Put folder in `htdocs/inventory`.
2. Open `http://localhost/phpmyadmin`.
3. Import `database.sql`.
4. Update DB settings in `includes/db.php` if needed.
5. Open app: `http://localhost/inventory`.

## Default Admin Login
- Email: `admin@shop.com`
- Password: `admin123`

## Main Pages
- `/inventory/login.php`
- `/inventory/register.php`
- `/inventory/admin/dashboard.php`
- `/inventory/customer/dashboard.php`
- `/inventory/customer/products.php`
- `/inventory/customer/cart.php`

