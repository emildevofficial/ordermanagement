Order Management System
A PHP-based web application for managing products, customers, orders, inventory, returns, and business analytics.

PHP
Mezzio
MySQL
Docker

Overview
The system centralizes the main operations of a small online business.

Administrators can manage products, customers, stock, orders, returns, promotions, and analytics. Users can browse products, place orders, track purchases, and request returns.

Main Features
<table> <tr> <td width="50%" valign="top">
Administrator
Dashboard and analytics
Product and inventory management
Customer management
Order processing
Return approval
CSV/XLSX import and export
Promotion settings
</td> <td width="50%" valign="top">
User
Registration and login
Browse products
Place orders
View order history
Cancel pending orders
Submit return requests
Manage profile
</td> </tr> </table>
Architecture


Browser
Nginx
PHP-FPM
Laminas Mezzio
Middleware
Handlers
PHP Templates
MySQL
Order Workflow


Browse Product
Place Order
Validate Stock
Create Order
Reduce Inventory
Order History
Technology Stack
Technology	Purpose
PHP 8.2+	Backend development
Laminas Mezzio	Application framework
MySQL	Database
PDO	Database access
Nginx	Web server
PHP-FPM	PHP runtime
Docker Compose	Local environment
Run the Project
1. Clone the repository
git clone https://github.com/emildevofficial/ordermanagement.git
cd ordermanagement
2. Start the containers
docker compose up -d --build
3. Open the application
Service	Address
Application	http://localhost:8080
phpMyAdmin	http://localhost:8081
Stop the project
docker compose down
Project Structure
ordermanagement/
|-- docker/
|-- src/
|   |-- config/
|   |-- public/
|   |-- src/App/
|   |   |-- Handler/
|   |   |-- Middleware/
|   |   |-- Database/
|   |   `-- templates/
|   `-- test/
|-- Dockerfile
`-- docker-compose.yml
Quality Commands
Run from the src directory:

composer test
composer cs-check
composer static-analysis
Author
Emiliano Duma

GitHub Profile
