# Realman Livestock Management System

A modern, scalable Laravel 10 application for managing a small-scale livestock and groceries business with MySQL backend and Blade frontend.

## 🎯 Project Overview

Realman Livestock is a comprehensive business management system designed for livestock businesses that handle:
- **Livestock**: Goats, Sheep, Fowls, Guinea Fowls, Turkeys, Rabbits, etc.
- **Processing Services**: Animal dressing and processing with quality control
- **Freezer Storage**: FIFO inventory management with expiration tracking
- **Groceries**: Store items and supplies
- **Order Management**: Complete order lifecycle from placement to delivery

## ✨ Key Features

### 🔐 Authentication & Authorization
- **Laravel Breeze** for authentication
- **Spatie Laravel Permission** for role-based access control
- **Roles**: Admin, Manager, Staff, Customer
- **Phone OTP Verification** for user signup
- **Admin Approval Workflow** for new users
- **Pending Approval Page** for users awaiting approval

### 🐐 Animal Management
- Comprehensive animal tracking with categories
- Health records and vaccination tracking
- Weight monitoring with history
- Status tracking (Available, Quarantined, Under Treatment, Reserved, Sold, Deceased)
- Photo uploads for animals
- Pricing management (per kg or fixed price)

### 📦 Order Management
- Public order placement system
- Order status tracking (Pending → Processing → Payment → Delivery → Completed)
- Order history with audit trail
- Payment tracking (Cash, Bank Transfer, Mobile Money, Credit)
- Delivery management (Pickup or Delivery)
- Special instructions and notes

### 🥩 Processing Services
- Animal dressing/processing request management
- Processing scheduling and cost calculation
- Live weight vs. dressed weight tracking
- Quality control notes
- Automatic freezer inventory creation upon completion

### ❄️ Freezer Inventory Management
- Batch tracking with unique batch numbers
- FIFO (First In, First Out) management
- Expiration date monitoring and alerts
- Storage location and temperature zone tracking
- Stock status management

### 🏪 Store Items Management
- Grocery and supplies inventory
- Low stock alerts
- Reorder level management
- SKU tracking
- Supplier information

### 👥 Customer Management
- Customer directory with contact information
- Purchase history tracking
- Credit management (credit limit & outstanding balance)
- Delivery and processing preferences
- Customer notes and special requirements

### 📊 Dashboard & Analytics
- Real-time business statistics
- Revenue tracking (daily, monthly, yearly)
- Low stock alerts
- Expiring inventory notifications
- Top-selling products analysis
- Recent activities feed

## 🏗️ Architecture & Design Patterns

### Loose Coupling with Service Layer
All business logic is encapsulated in service classes for better maintainability and testability:

- **AnimalService**: Animal management, health records, weight tracking
- **OrderService**: Order creation, status updates, payment processing
- **CustomerService**: Customer CRUD, purchase history, balance management
- **InventoryService**: Freezer and store inventory management, stock alerts
- **ProcessingService**: Processing request lifecycle management
- **DashboardService**: Dashboard data aggregation and analytics

### Database Schema
Comprehensive database design with proper relationships:

```
- categories (livestock, grocery, service)
- animals (with soft deletes)
- customers (with soft deletes)
- orders (with soft deletes)
- order_items (polymorphic relationships)
- order_status_histories (audit trail)
- processing_requests
- freezer_inventories
- store_items
- health_records
- weight_records
- users (with approval workflow)
- roles & permissions (Spatie)
```

## 🎨 Frontend Design

### Dark Modern Theme
- **Tailwind CSS** for styling
- Responsive mobile-first design
- Gradient accents (Indigo/Purple/Pink)
- Glass-morphism effects
- Smooth animations and transitions

### Landing Page Sections
1. **Hero Section**: Compelling call-to-action with stats
2. **Product Categories**: Livestock and groceries showcase
3. **Featured Products**: Dynamic product listings
4. **About Section**: Business highlights with quality assurance
5. **Contact Form**: Easy customer inquiries
6. **Footer**: Quick links and social media

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Node.js & NPM

### Installation Steps

1. **Clone the repository** (already done)

2. **Configure Environment**
   ```bash
   # Update .env file with your database credentials
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=realman_live_db
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

4. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

   This will create:
   - All database tables
   - Roles & Permissions
   - Sample categories (Livestock & Groceries)
   - Admin, Manager, and Staff users

6. **Link Storage**
   ```bash
   php artisan storage:link
   ```

7. **Build Assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

8. **Start Development Server**
   ```bash
   php artisan serve
   ```

Visit: http://localhost:8000

## 👤 Default Users

After seeding, you can login with:

**Admin**
- Email: admin@realman.com
- Password: password

**Manager**
- Email: manager@realman.com
- Password: password

**Staff**
- Email: staff@realman.com
- Password: password

## 📋 Permissions Structure

### Admin
- Full system access
- User management and approval
- Financial reporting
- System configuration

### Manager
- Animal inventory management
- Sales processing
- Customer management
- Reporting access
- Staff supervision

### Staff
- Basic animal entry
- Sales recording
- Customer information updates
- Limited reporting access

### Customer
- View orders
- Create orders
- Track order status

## 🛠️ Technology Stack

- **Backend**: Laravel 10
- **Database**: MySQL
- **Frontend**: Blade Templates
- **CSS Framework**: Tailwind CSS
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Laravel Permission
- **Development Tools**: 
  - Laravel Debugbar
  - Laravel IDE Helper
  - Laravel Pint (Code Style)

## 📁 Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       └── Admin/
│           ├── DashboardController.php
│           ├── AnimalController.php
│           └── OrderController.php
├── Models/
│   ├── Animal.php
│   ├── Category.php
│   ├── Customer.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── ProcessingRequest.php
│   ├── FreezerInventory.php
│   ├── StoreItem.php
│   ├── HealthRecord.php
│   ├── WeightRecord.php
│   └── User.php
└── Services/
    ├── AnimalService.php
    ├── OrderService.php
    ├── CustomerService.php
    ├── InventoryService.php
    ├── ProcessingService.php
    └── DashboardService.php

database/
├── migrations/
│   ├── *_create_categories_table.php
│   ├── *_create_animals_table.php
│   ├── *_create_customers_table.php
│   ├── *_create_orders_table.php
│   ├── *_create_order_items_table.php
│   ├── *_create_processing_requests_table.php
│   ├── *_create_freezer_inventories_table.php
│   ├── *_create_store_items_table.php
│   ├── *_create_order_status_histories_table.php
│   ├── *_create_health_records_table.php
│   ├── *_create_weight_records_table.php
│   └── *_add_fields_to_users_table.php
└── seeders/
    ├── RolePermissionSeeder.php
    ├── CategorySeeder.php
    └── AdminUserSeeder.php

resources/
└── views/
    └── welcome.blade.php (Modern Dark Landing Page)
```

## 🔄 Next Steps

### Immediate Tasks
1. **Configure Database**: Update .env with MySQL credentials
2. **Run Migrations**: Execute `php artisan migrate:fresh --seed`
3. **Test Landing Page**: Verify responsive design
4. **Setup OTP Service**: Integrate SMS provider for phone verification

### Pending Implementation
1. **Public Order Form**: Complete order placement interface
2. **Admin Dashboard**: Full dashboard with charts and analytics
3. **Animal CRUD**: Complete animal management interface
4. **Order Management**: Full order processing workflow
5. **Customer Portal**: Customer-facing order tracking
6. **Reports & Analytics**: PDF/Excel export functionality
7. **Processing Module**: Complete processing request workflow
8. **Inventory Management**: Freezer and store item interfaces
9. **Settings Page**: System configuration panel
10. **Email Notifications**: Order confirmations and updates
11. **SMS Integration**: Phone verification and order notifications

## 🎯 Business Workflows

### Order Lifecycle
1. **Customer places order** → Status: Pending
2. **Staff processes order** → Status: Processing
3. **Payment received** → Status: Payment Received
4. **Prepared for delivery** → Status: Ready for Delivery
5. **Out for delivery** → Status: Out for Delivery
6. **Delivered** → Status: Delivered

### Processing Workflow
1. **Processing request created**
2. **Scheduled for processing**
3. **Processing in progress**
4. **Quality control check**
5. **Completed & added to freezer inventory**

## 📈 Performance & Scalability

- **Eager Loading**: Relationships are eager-loaded to prevent N+1 queries
- **Indexed Columns**: Foreign keys and frequently queried columns are indexed
- **Soft Deletes**: Data preservation with ability to restore
- **Service Layer**: Business logic separated from controllers
- **Caching Ready**: Structure supports Redis/Memcached integration
- **Queue Ready**: Long-running tasks can be queued

## 🔒 Security Features

- **CSRF Protection**: All forms protected
- **SQL Injection Prevention**: Eloquent ORM and prepared statements
- **XSS Protection**: Blade template escaping
- **Role-Based Access Control**: Spatie permissions
- **Password Hashing**: Bcrypt hashing
- **Soft Deletes**: No permanent data loss

## 📝 Notes

- All timestamps are automatically managed by Laravel
- Soft deletes are implemented on major tables for data recovery
- The system is designed to be easily scalable
- Service layer architecture ensures loose coupling
- Mobile-responsive design for all device sizes

## 🤝 Support

For support or questions, contact the development team.

---

Built with ❤️ for Realman Livestock Business
