Order Management System
Order Management System is a backend-focused PHP application for centralizing core order operations in a small business workflow. It brings product inventory, customer records, order processing, returns, promotion settings, and operational analytics into one server-rendered web application.

The project is built with PHP 8.2+, Laminas Mezzio, MySQL, PDO, Docker, Docker Compose, Nginx, and PHP-FPM. It follows Mezzio's middleware-driven request lifecycle, uses session-based authentication, and renders HTML views through PHP templates.

Project Overview
This system supports a practical order management workflow:

Administrators manage products, stock levels, customers, orders, returns, promotion settings, and analytics.
Standard users register, log in, browse active products, place product orders, review their order history, and submit return requests for eligible completed or delivered orders.
Orders connect users, customer profiles, order items, product stock, and return records in a MySQL-backed data model.
Inventory changes are reflected during checkout, order cancellation, product restocking, and approved returns.
Analytics and dashboard pages summarize orders, revenue, customers, products, returns, inventory risk, and recent activity.
The application is primarily server-rendered. Routes are handled by Mezzio request handlers, protected by custom authentication and role middleware, and backed by direct PDO queries through a small database wrapper.

Key Features
Administrator Features
Session-based login and logout.
Admin role authorization through RoleMiddleware.
Dashboard statistics for orders, revenue, returns, customers, product inventory, low-stock products, and recent orders.
Analytics page with:
revenue and order summaries
daily and monthly order/revenue trends
order status breakdowns
top-selling and top-revenue products
slow-moving and inventory-risk products
customer spending and order-count analytics
return status and returned-product analytics
recent high-value orders
Product management:
create products
view products
edit product name, price, stock, and image URL
toggle products active or inactive
increment stock and track restock metadata
bulk edit selected products
Product import/export tools:
export products as CSV
export products as XLSX
download an XLSX import sample
import products from CSV to create or update inventory
Customer management:
list registered customer accounts
create customer records
view customer details, order history, ordered item totals, and spending summary
Order administration:
view all orders
view order details
edit order items, notes, totals, and limited statuses
complete pending orders
cancel pending orders and restore product stock
Return management:
view all return requests
inspect return details and related order items
approve or reject pending returns
store admin notes
restore stock when a return is approved
Promotion settings:
enable or disable a new-user discount
configure the discount percentage
apply the discount to a user's purchase flow when enabled and the user has no completed orders
Profile and password management.
User Features
Registration with validation for required fields, email format, duplicate email addresses, password length, and password confirmation.
Automatic customer-profile creation during registration.
Session-based login and logout.
Browse active products in the shop.
Place orders for active products.
Checkout validation for selected product, quantity, product availability, and stock.
Automatic stock deduction when an order is placed.
View personal order history.
View order details.
Cancel pending orders and restore stock.
Submit one return request per eligible order.
View personal return requests and return details.
Edit profile name.
Change password after current-password verification.
Application Controls and Validation
Protected routes redirect unauthenticated users to /login.
Admin-only routes redirect standard users away from restricted pages.
Passwords are stored with PHP password hashing.
PDO prepared statements are used throughout the handlers.
Flash messages are used for user-facing validation and workflow feedback.
Several order, product, return, and profile actions use transactions to keep stock and related records consistent.
Architecture
The project is a Laminas Mezzio application using PSR-7 requests/responses, PSR-15 middleware, and a Laminas ServiceManager dependency injection container.



Browser
Nginx
PHP-FPM
public/index.php
Mezzio Application
Global Middleware Pipeline
Route Matching
AuthMiddleware
RoleMiddleware whererequired
Route Handlers
PHP Templates
Database PDO Wrapper
MySQL
Request Lifecycle
Nginx serves requests from src/public and forwards PHP requests to PHP-FPM.
src/public/index.php loads Composer autoloading, builds the dependency container, registers the middleware pipeline, registers routes, and runs the Mezzio application.
config/pipeline.php applies Mezzio's error handler, URL/server helpers, parsed-body middleware, route middleware, method handling, URL helper middleware, dispatch middleware, and not-found fallback.
config/routes.php maps HTTP routes to handlers and attaches AuthMiddleware and RoleMiddleware where required.
Handlers execute business logic, query MySQL through App\Database\Database, and return HTML, redirects, JSON, CSV, or XLSX responses.
PHP templates under src/src/App/templates render the server-side UI.
Main Architectural Components
src/public/index.php - application entry point.
src/config - Mezzio configuration, routes, middleware pipeline, and container setup.
src/config/autoload/database.global.php - database configuration using environment variables, Railway-style variables, or DATABASE_URL / MYSQL_URL.
src/src/App/Middleware - custom authentication and role authorization middleware.
src/src/App/Handler - route handlers grouped by domain: auth, dashboard, analytics, products, customers, orders, returns, settings, and profile.
src/src/App/Database/Database.php - PDO connection wrapper and user lookup helper.
src/src/App/Helper - session, permissions, templates, and date/time helpers.
src/src/App/templates - PHP templates for server-rendered views.
docker - Nginx, MySQL initialization, PHP-FPM, and runtime support files.
Technology Stack
PHP 8.2+; Composer is configured for PHP ~8.2.0 || ~8.3.0 || ~8.4.0 || ~8.5.0
Laminas Mezzio
Laminas Diactoros
Laminas ServiceManager
Mezzio FastRoute router
MySQL 8.0
PDO / PDO MySQL
Docker and Docker Compose
Nginx
PHP-FPM
PhpSpreadsheet for XLSX export and import-template generation
PHPUnit for tests
PHP_CodeSniffer with Laminas Coding Standard
Psalm static analysis
Tailwind CSS CDN in server-rendered templates
Repository Structure
.
+-- docker/
|   +-- mysql/init.sql
|   +-- nginx/default.conf
|   +-- php-fpm/railway.conf
|   +-- start.sh
+-- src/
|   +-- bin/
|   +-- config/
|   +-- database/
|   +-- public/
|   +-- src/App/
|   |   +-- Database/
|   |   +-- Handler/
|   |   +-- Helper/
|   |   +-- Middleware/
|   |   +-- templates/
|   +-- test/
|   +-- composer.json
|   +-- phpunit.xml.dist
|   +-- phpcs.xml.dist
|   +-- psalm.xml.dist
+-- docker-compose.yml
+-- Dockerfile
+-- README.md
Database
The Docker MySQL service loads docker/mysql/init.sql on first startup. The script creates the order_management database and the main tables:

users
customers
products
orders
order_items
returns
promotion_settings
The script also includes runtime-safe migration logic for existing databases and inserts a default local admin user:

Email: admin@example.com
Password hash: stored in docker/mysql/init.sql
Role: admin
Additional SQL files in src/database document or apply incremental database changes, including customer/profile mapping, timestamp consistency, return-flow consistency, product restock metadata, and legacy data fixes.

Routes
The primary routes are defined in src/config/routes.php.

Public Routes
GET / - login page
GET /login - login page
POST /login - login submit
GET /register - redirects to the register tab on the login page
POST /register - register user
GET /logout - logout
Protected User and Shared Routes
GET /shop - product browsing/shop view
POST /shop/buy - create order from product purchase
GET /my-orders - current user's orders
GET /orders/{id} - order detail
GET|POST /orders/{id}/edit - edit an order
POST /orders/{id}/cancel - cancel a pending order
GET /returns - return list for current user or admin
GET|POST /returns/create - submit a return request
GET /returns/{id} - return detail
GET /profile - profile page
GET|POST /profile/edit - edit profile name
GET|POST /profile/password - change password
GET|POST /settings - account settings, with promotion settings shown only to admins
GET|POST /settings/password - settings password form
Administrator-Facing Routes
These routes are used by the administrator workflow. Some are protected directly with RoleMiddleware; others enforce role checks inside the handler or are only exposed through admin navigation.

GET /dashboard
GET /analytics
GET /orders
POST /orders/{id}/update
POST /orders/{id}/delete - cancels eligible pending orders rather than physically deleting records
POST /orders/{id}/status-action
GET /customers
GET|POST /customers/create
GET /customers/{id}
GET /products
GET|POST /products/create
GET|POST /products/{id}/edit
POST /products/{id}/stock
POST /products/{id}/toggle
GET /import-export
GET /import-export/export
GET /import-export/export-xlsx
GET /import-export/sample
POST /import-export/import
POST /import-export/bulk-edit
POST /returns/{id}/update
Getting Started
Prerequisites
Docker
Docker Compose
For running tools directly outside Docker:

PHP 8.2 or newer
Composer
MySQL-compatible database
Run with Docker Compose
From the repository root:

docker compose up --build
Services exposed by docker-compose.yml:

Application: http://localhost:8080
phpMyAdmin: http://localhost:8081
MySQL host port: 3307
The application container mounts ./src into /var/www/html, and Nginx serves the Mezzio public entry point from /var/www/html/public.

Database Configuration
Default Docker database values are:

Database: order_management
Host from PHP container: mysql
Port from PHP container: 3306
Host port: 3307
User: root
Password: empty
The application reads database settings from these environment variables:

DB_HOST
DB_PORT
DB_NAME
DB_USER
DB_PASS
It also supports Railway/MySQL-style variables and connection URLs:

MYSQLHOST
MYSQLPORT
MYSQLDATABASE
MYSQLUSER
MYSQLPASSWORD
DATABASE_URL
MYSQL_URL
Local Composer Commands
From the src directory:

composer install
composer serve
The composer serve command starts PHP's built-in server on 0.0.0.0:8080 with src/public as the document root. A MySQL database must still be available and configured for the app to run correctly.

Quality and Testing
The repository includes PHPUnit, PHP_CodeSniffer, and Psalm configuration.

Run these commands from src:

composer test
composer cs-check
composer static-analysis
composer check
Current tests cover the Mezzio skeleton-style home and ping handlers plus the analytics handler factory. The main business workflows are implemented in application handlers, but the current automated test suite does not yet provide broad coverage for orders, products, inventory, returns, authentication, or promotions.

Implementation Notes
The codebase uses route handlers directly for most business logic instead of a separate service/repository layer.
App\Database\Database owns the PDO connection and exposes getPdo() for handlers.
AuthMiddleware starts the session, checks user_id, and attaches user details to the request.
RoleMiddleware restricts admin routes by checking the admin role.
Product import/export uses PhpSpreadsheet for XLSX output and standard CSV parsing for imports.
Several handlers contain small schema-compatibility helpers that add missing columns at runtime for older databases.
Dates are stored in UTC and formatted in the configured application timezone Europe/Tirane.
Templates are plain PHP files and the shared layout loads Tailwind CSS from a CDN.
Current Limitations and Areas for Improvement
Product deletion is not implemented; products can be deactivated through the active/inactive toggle.
Order creation currently supports a single product item per purchase flow.
Order status handling is broader in the database schema than in the main UI/actions; the implemented status actions focus mainly on pending, completed, and cancelled.
Business logic is mostly located in handlers, so extracting service and repository classes would improve separation of concerns as the project grows.
Automated tests are limited and do not yet cover the major business workflows.
The README documents the current repository state; screenshots are not included because no screenshot assets are present in the repository.
License
The project includes src/LICENSE.md and src/COPYRIGHT.md from the Laminas Mezzio skeleton foundation.
