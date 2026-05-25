<p align="center">
<img src="https://laravel.com/img/logotype.min.svg" width="400">
</p>

<h1 align="center">Laptop Store Management System</h1>

<p align="center">
A modern web application built with <b>Laravel 12</b> for managing a laptop store.
</p>

<p align="center">
<img src="https://img.shields.io/badge/Laravel-12-red">
<img src="https://img.shields.io/badge/MySQL-Database-orange">
<img src="https://img.shields.io/badge/License-MIT-green">
</p>

---

# About Project

Laptop Store Management System is a web application designed to help manage laptop products, categories, orders, and customers efficiently.

The system allows users to browse products, add them to a cart, and complete the checkout process. Administrators can manage inventory and product information easily.

---

# Features

### User Features
- Browse laptop products
- View product details
- Add products to cart
- Update cart quantity
- Checkout system

### Admin Features
- Manage products (Create, Update, Delete)
- Manage categories
- Upload product images
- Product pagination
- Inventory management

---

# Installation Guide

## 1. Clone Repository

```
git clone https://github.com/sembunly/Ecom-Web.git
cd Ecom-Web
```

## 2. Install Dependencies

```
composer install
```

## 3. Create Environment File

```
cp .env.example .env
```

## 4. Generate Application Key

```
php artisan key:generate
```

## 5. Configure Database

Edit `.env`

```
DB_DATABASE=laptop_store
DB_USERNAME=root
DB_PASSWORD=
```

## 6. Run Migration

```
php artisan migrate
```

## 7. Run Development Server

```
php artisan serve
```

Open in browser

```
http://127.0.0.1:8000
```

---

Example:

```
Home Page
Product List
Shopping Cart
Checkout Page
Admin Dashboard
```
---

GitHub  
https://github.com/sembunly


