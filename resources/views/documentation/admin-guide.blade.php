<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin User Guide - Livestock Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .section { scroll-margin-top: 100px; }
        .nav-sticky { position: sticky; top: 0; z-index: 50; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <div class="nav-sticky bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold">Livestock Management System</h1>
            <p class="text-indigo-100 mt-1">Administrator User Guide v1.0</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Table of Contents -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">📚 Table of Contents</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="#overview" class="text-indigo-600 hover:text-indigo-800 hover:underline">1. System Overview</a>
                <a href="#dashboard" class="text-indigo-600 hover:text-indigo-800 hover:underline">2. Dashboard</a>
                <a href="#customers" class="text-indigo-600 hover:text-indigo-800 hover:underline">3. Customer Management</a>
                <a href="#orders" class="text-indigo-600 hover:text-indigo-800 hover:underline">4. Order Management</a>
                <a href="#animals" class="text-indigo-600 hover:text-indigo-800 hover:underline">5. Animals Management</a>
                <a href="#processing" class="text-indigo-600 hover:text-indigo-800 hover:underline">6. Processing Requests</a>
                <a href="#freezer" class="text-indigo-600 hover:text-indigo-800 hover:underline">7. Freezer Inventory</a>
                <a href="#store" class="text-indigo-600 hover:text-indigo-800 hover:underline">8. Store Items</a>
                <a href="#reports" class="text-indigo-600 hover:text-indigo-800 hover:underline">9. Reports & Analytics</a>
                <a href="#users" class="text-indigo-600 hover:text-indigo-800 hover:underline">10. User Management</a>
                <a href="#roles" class="text-indigo-600 hover:text-indigo-800 hover:underline">11. Roles & Permissions</a>
                <a href="#categories" class="text-indigo-600 hover:text-indigo-800 hover:underline">12. Categories</a>
            </div>
        </div>

        <!-- 1. System Overview -->
        <div id="overview" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">1️⃣ System Overview</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">The Livestock Management System is a comprehensive solution designed for managing livestock operations in Ghana, with full support for Ghana Cedis (GHS) currency.</p>
                
                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Key Features:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li><strong>Customer Management:</strong> Track customer information, credit limits, and preferences</li>
                    <li><strong>Order Processing:</strong> Create and manage orders with dynamic item calculation</li>
                    <li><strong>Animal Inventory:</strong> Monitor livestock with unique tagging and weight tracking</li>
                    <li><strong>Processing Workflow:</strong> Track dressing efficiency and processing requests</li>
                    <li><strong>Freezer Management:</strong> Control cold storage inventory with expiry tracking</li>
                    <li><strong>Store Items:</strong> Manage additional products with stock adjustment capabilities</li>
                    <li><strong>Comprehensive Reports:</strong> Generate sales, inventory, customer, processing, and financial reports</li>
                    <li><strong>User & Role Management:</strong> Control access with role-based permissions</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Technology Stack:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li>Laravel 10+ Framework</li>
                    <li>Dark Theme UI with Tailwind CSS</li>
                    <li>UUID-based Security</li>
                    <li>Excel & PDF Export Capabilities</li>
                    <li>Real-time Chart.js Visualizations</li>
                </ul>
            </div>
        </div>

        <!-- 2. Dashboard -->
        <div id="dashboard" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">2️⃣ Dashboard</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">The dashboard provides an at-a-glance view of your business operations.</p>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Dashboard Features:</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">📊 Statistics Cards:</h4>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li><strong>Total Revenue:</strong> Sum of all order totals</li>
                        <li><strong>Total Orders:</strong> Count of all orders in the system</li>
                        <li><strong>Total Customers:</strong> Number of registered customers</li>
                        <li><strong>Pending Orders:</strong> Orders with "pending" status</li>
                    </ul>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">📈 Sales Trend Chart:</h4>
                    <p class="text-gray-700">Interactive line chart showing daily sales for the last 7 days, helping you identify sales patterns and trends.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">🔔 Recent Activities:</h4>
                    <p class="text-gray-700">Lists the 5 most recent orders with customer names, amounts, and timestamps for quick overview of business activity.</p>
                </div>
            </div>
        </div>

        <!-- 3. Customer Management -->
        <div id="customers" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">3️⃣ Customer Management</h2>
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Adding a Customer:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Navigate to <strong>Customers</strong> → Click <strong>"+ Add Customer"</strong></li>
                    <li>Fill in required fields:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Name, Email, Phone (required)</li>
                            <li>Address, Preferred Delivery, Preferred Processing</li>
                        </ul>
                    </li>
                    <li>Credit Settings:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Toggle "Allow Credit" if customer can buy on credit</li>
                            <li>Set Credit Limit (in GHS)</li>
                        </ul>
                    </li>
                    <li>Click <strong>"Create Customer"</strong></li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Managing Customers:</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">👁️ View Customer:</h4>
                    <p class="text-gray-700">Click the eye icon to see detailed customer information including all associated orders.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">✏️ Edit Customer:</h4>
                    <p class="text-gray-700">Click the edit icon to update customer details, credit settings, or toggle active status.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-800 mb-2">🗑️ Delete Customer:</h4>
                    <p class="text-gray-700">Click the delete icon to remove a customer. Note: Customers with existing orders cannot be deleted.</p>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                    <p class="text-blue-800"><strong>💡 Tip:</strong> Use the search box to quickly find customers by name, email, or phone number.</p>
                </div>
            </div>
        </div>

        <!-- 4. Order Management -->
        <div id="orders" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">4️⃣ Order Management</h2>
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Creating an Order:</h3>
                <ol class="list-decimal list-inside space-y-3 text-gray-700 mb-4">
                    <li><strong>Customer Selection:</strong>
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Select customer from dropdown</li>
                            <li>Phone number and address auto-fill from customer profile</li>
                        </ul>
                    </li>
                    <li><strong>Delivery Information:</strong>
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Choose delivery type: Pickup or Delivery</li>
                            <li>Set delivery date (optional)</li>
                            <li>Confirm or update delivery address</li>
                        </ul>
                    </li>
                    <li><strong>Adding Order Items:</strong>
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Click "+ Add Item" button</li>
                            <li>Select animal from available livestock</li>
                            <li>Set quantity</li>
                            <li>Choose processing type (Live or Dressed)</li>
                            <li>Price calculates automatically based on animal weight and pricing</li>
                            <li>Add multiple items as needed</li>
                        </ul>
                    </li>
                    <li><strong>Payment & Notes:</strong>
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Select payment method: Cash, Mobile Money, Bank Transfer, or Credit</li>
                            <li>Add special instructions for the order</li>
                            <li>Add internal notes (not visible to customer)</li>
                        </ul>
                    </li>
                    <li>Review total amount and click <strong>"Create Order"</strong></li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Order Statuses:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3">
                        <p class="font-semibold text-yellow-800">Pending</p>
                        <p class="text-sm text-yellow-700">Order placed, awaiting processing</p>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3">
                        <p class="font-semibold text-blue-800">Processing</p>
                        <p class="text-sm text-blue-700">Order being prepared</p>
                    </div>
                    <div class="bg-purple-50 border-l-4 border-purple-500 p-3">
                        <p class="font-semibold text-purple-800">Payment Received</p>
                        <p class="text-sm text-purple-700">Customer has paid</p>
                    </div>
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-3">
                        <p class="font-semibold text-indigo-800">Ready for Delivery</p>
                        <p class="text-sm text-indigo-700">Order ready to be delivered</p>
                    </div>
                    <div class="bg-teal-50 border-l-4 border-teal-500 p-3">
                        <p class="font-semibold text-teal-800">Out for Delivery</p>
                        <p class="text-sm text-teal-700">Order is being delivered</p>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-3">
                        <p class="font-semibold text-green-800">Delivered</p>
                        <p class="text-sm text-green-700">Order completed successfully</p>
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Order Filters:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li><strong>Status Filter:</strong> View orders by specific status</li>
                    <li><strong>Date Range:</strong> Filter orders between specific dates</li>
                    <li><strong>Search:</strong> Find orders by order ID or customer name</li>
                </ul>
            </div>
        </div>

        <!-- 5. Animals Management -->
        <div id="animals" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">5️⃣ Animals Management</h2>
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Adding an Animal:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Navigate to <strong>Animals</strong> → Click <strong>"+ Add Animal"</strong></li>
                    <li>Enter animal details:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Tag Number (unique identifier)</li>
                            <li>Animal Type (cattle, goat, sheep, pig, etc.)</li>
                            <li>Breed, Gender, Age</li>
                            <li>Purchase Weight & Current Weight (kg)</li>
                            <li>Purchase Price & Date</li>
                            <li>Health Status, Vaccination Records</li>
                        </ul>
                    </li>
                    <li>Pricing:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Enter Selling Price per kg</li>
                            <li>Or enter Fixed Selling Price for entire animal</li>
                        </ul>
                    </li>
                    <li>Click <strong>"Create Animal"</strong></li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Animal Statuses:</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="font-semibold text-green-800">Available</p>
                        <p class="text-sm text-green-700">Ready for sale</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                        <p class="font-semibold text-yellow-800">Reserved</p>
                        <p class="text-sm text-yellow-700">Held for customer</p>
                    </div>
                    <div class="bg-gray-50 border border-gray-200 rounded p-3">
                        <p class="font-semibold text-gray-800">Sold</p>
                        <p class="text-sm text-gray-700">Transaction completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Processing Requests -->
        <div id="processing" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">6️⃣ Processing Requests</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Track animal processing and measure dressing efficiency.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Creating a Processing Request:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Select customer and animal</li>
                    <li>Choose processing type: Live, Dressed, or Both</li>
                    <li>Enter weights:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Live Weight (kg)</li>
                            <li>Dressed Weight (kg) - if applicable</li>
                        </ul>
                    </li>
                    <li>Set processing fee and date</li>
                    <li>Add special requirements if any</li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Key Metrics:</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-700"><strong>Dressing Percentage:</strong> Automatically calculated as (Dressed Weight / Live Weight × 100). This metric helps track processing efficiency and product yield.</p>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Processing Statuses:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li><strong>Pending:</strong> Request submitted, not started</li>
                    <li><strong>In Progress:</strong> Currently being processed</li>
                    <li><strong>Completed:</strong> Processing finished</li>
                    <li><strong>Cancelled:</strong> Request cancelled</li>
                </ul>
            </div>
        </div>

        <!-- 7. Freezer Inventory -->
        <div id="freezer" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">7️⃣ Freezer Inventory</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Manage cold storage inventory with automatic expiry tracking.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Adding Freezer Inventory:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Enter batch number (auto-generated if left blank)</li>
                    <li>Select product category</li>
                    <li>Enter product name and description</li>
                    <li>Input weight (kg) and pricing:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Cost Price</li>
                            <li>Selling Price per kg</li>
                        </ul>
                    </li>
                    <li>Set dates:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Processing Date</li>
                            <li>Expiry Date (system highlights expiring items)</li>
                        </ul>
                    </li>
                    <li>Specify storage location and add quality notes</li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Inventory Features:</h3>
                <div class="space-y-3">
                    <div class="bg-blue-50 rounded p-3">
                        <p class="font-semibold text-blue-800">🔍 Advanced Filters</p>
                        <p class="text-sm text-blue-700">Filter by category, status, or search by batch number/product name</p>
                    </div>
                    <div class="bg-orange-50 rounded p-3">
                        <p class="font-semibold text-orange-800">⚠️ Expiry Alerts</p>
                        <p class="text-sm text-orange-700">Automatic highlighting of items expiring within 7 days</p>
                    </div>
                    <div class="bg-purple-50 rounded p-3">
                        <p class="font-semibold text-purple-800">📊 Stock Value</p>
                        <p class="text-sm text-purple-700">Automatic calculation of total inventory value</p>
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Inventory Statuses:</h3>
                <ul class="list-disc list-inside space-y-1 text-gray-700">
                    <li><strong>In Stock:</strong> Available for sale</li>
                    <li><strong>Reserved:</strong> Held for specific order</li>
                    <li><strong>Sold:</strong> Inventory sold</li>
                    <li><strong>Expired:</strong> Past expiry date</li>
                </ul>
            </div>
        </div>

        <!-- 8. Store Items -->
        <div id="store" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">8️⃣ Store Items</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Manage additional products and supplies with automated stock tracking.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Adding Store Items:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Enter SKU (Stock Keeping Unit) and item name</li>
                    <li>Select category and add description</li>
                    <li>Set inventory details:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Current Quantity</li>
                            <li>Unit of Measurement</li>
                            <li>Reorder Level (triggers low stock alert)</li>
                        </ul>
                    </li>
                    <li>Enter pricing:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Cost Price (purchase cost)</li>
                            <li>Selling Price (retail price)</li>
                        </ul>
                    </li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Stock Adjustment:</h3>
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-gray-700 mb-2">Click the <strong>"Adjust Stock"</strong> button to:</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li><strong>Add:</strong> Increase stock (e.g., new delivery)</li>
                        <li><strong>Subtract:</strong> Decrease stock (e.g., damage, theft)</li>
                        <li><strong>Set:</strong> Override with exact quantity (e.g., after physical count)</li>
                    </ul>
                    <p class="text-sm text-gray-600 mt-2">Always include adjustment reason for audit trail.</p>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Low Stock Alerts:</h3>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                    <p class="text-yellow-800">Items below reorder level are automatically highlighted with a yellow "Low Stock" badge for easy identification.</p>
                </div>
            </div>
        </div>

        <!-- 9. Reports & Analytics -->
        <div id="reports" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">9️⃣ Reports & Analytics</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Comprehensive reporting system with Excel and PDF export capabilities.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Report Types:</h3>

                <div class="space-y-4">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">📊 Sales Report</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Daily sales trend (line chart)</li>
                            <li>Top customers by revenue</li>
                            <li>Sales breakdown by status</li>
                            <li>Total revenue and payment summary</li>
                        </ul>
                        <p class="text-sm text-gray-600 mt-2"><strong>Exports:</strong> Excel, PDF</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">📦 Inventory Report</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Store items by category with stock value</li>
                            <li>Low stock and out-of-stock items</li>
                            <li>Freezer inventory by status</li>
                            <li>Items expiring soon (within 7 days)</li>
                            <li>Animals inventory breakdown</li>
                        </ul>
                        <p class="text-sm text-gray-600 mt-2"><strong>Exports:</strong> Excel (multi-sheet)</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">👥 Customer Report</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Top 10 customers ranked by revenue</li>
                            <li>Customer growth metrics</li>
                            <li>Credit analysis (total limits, used, available)</li>
                            <li>Active customer tracking</li>
                        </ul>
                        <p class="text-sm text-gray-600 mt-2"><strong>Exports:</strong> Excel</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">🔪 Processing Report</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Processing efficiency metrics</li>
                            <li>Average dressing percentage</li>
                            <li>Processing by status (bar chart)</li>
                            <li>Monthly processing trends</li>
                        </ul>
                        <p class="text-sm text-gray-600 mt-2"><strong>Exports:</strong> Excel</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-2">💰 Financial Report</h4>
                        <ul class="list-disc list-inside text-gray-700 space-y-1">
                            <li>Revenue analysis (sales, paid, outstanding)</li>
                            <li>Processing and delivery fees summary</li>
                            <li>Inventory valuation (store items + freezer)</li>
                            <li>Monthly revenue trend (line chart)</li>
                            <li>Payment status breakdown</li>
                        </ul>
                        <p class="text-sm text-gray-600 mt-2"><strong>Exports:</strong> Excel, PDF</p>
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Using Reports:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Select date range (defaults to current month)</li>
                    <li>Click "Apply Filter" to refresh data</li>
                    <li>View interactive charts and tables</li>
                    <li>Click "Export Excel" for spreadsheet export (green button)</li>
                    <li>Click "Print PDF" for printable report (red button - where available)</li>
                </ol>

                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mt-4">
                    <p class="text-blue-800"><strong>💡 Tip:</strong> All monetary values are displayed in Ghana Cedis (GHS). Charts use dark theme colors for consistency with the system interface.</p>
                </div>
            </div>
        </div>

        <!-- 10. User Management -->
        <div id="users" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">🔟 User Management</h2>
            <div class="prose max-w-none">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">User Account Features:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li><strong>Pending Users:</strong> View and approve/reject new user registrations</li>
                    <li><strong>Active Users:</strong> Manage existing user accounts</li>
                    <li><strong>Role Assignment:</strong> Assign roles to control access levels</li>
                    <li><strong>Account Suspension:</strong> Temporarily disable user access</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Approving New Users:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700">
                    <li>Navigate to <strong>Users</strong> → <strong>Pending Users</strong></li>
                    <li>Review user details and registration information</li>
                    <li>Click "Approve" to grant access or "Reject" to deny</li>
                    <li>Assign appropriate role during approval</li>
                </ol>
            </div>
        </div>

        <!-- 11. Roles & Permissions -->
        <div id="roles" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">1️⃣1️⃣ Roles & Permissions</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Control system access using role-based permissions powered by Spatie Permission package.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Creating a Role:</h3>
                <ol class="list-decimal list-inside space-y-2 text-gray-700 mb-4">
                    <li>Navigate to <strong>Roles & Permissions</strong> → Click <strong>"+ Create Role"</strong></li>
                    <li>Enter role name (e.g., "Manager", "Staff", "Accountant")</li>
                    <li>Select permissions from available list:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>View permissions (read access)</li>
                            <li>Create permissions (add new records)</li>
                            <li>Edit permissions (modify existing records)</li>
                            <li>Delete permissions (remove records)</li>
                        </ul>
                    </li>
                    <li>Click "Create Role" to save</li>
                </ol>

                <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Permission Categories:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">Customers Management</p>
                        <p class="text-sm text-gray-600">Create, view, edit, delete customers</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">Orders Management</p>
                        <p class="text-sm text-gray-600">Process and manage orders</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">Inventory Management</p>
                        <p class="text-sm text-gray-600">Animals, freezer, store items</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">Reports Access</p>
                        <p class="text-sm text-gray-600">View and export reports</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">User Management</p>
                        <p class="text-sm text-gray-600">Manage users and roles</p>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="font-semibold text-gray-800">Settings</p>
                        <p class="text-sm text-gray-600">System configuration</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12. Categories -->
        <div id="categories" class="section bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b-2 border-indigo-600 pb-2">1️⃣2️⃣ Categories</h2>
            <div class="prose max-w-none">
                <p class="text-gray-700 mb-4">Organize products using categories for better inventory management.</p>

                <h3 class="text-xl font-semibold text-gray-800 mb-3">Category Management:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700">
                    <li>Create categories for different product types</li>
                    <li>Add descriptions for clarity</li>
                    <li>Toggle active/inactive status</li>
                    <li>Categories are used across:
                        <ul class="list-disc list-inside ml-6 mt-2">
                            <li>Freezer Inventory</li>
                            <li>Store Items</li>
                            <li>Product Classification</li>
                        </ul>
                    </li>
                </ul>

                <div class="bg-green-50 border-l-4 border-green-500 p-4 mt-4">
                    <p class="text-green-800"><strong>✅ Best Practice:</strong> Create categories before adding inventory items for better organization and reporting accuracy.</p>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
            <h2 class="text-2xl font-bold mb-4">📋 System Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="font-semibold">Version:</p>
                    <p class="text-indigo-100">1.0.0</p>
                </div>
                <div>
                    <p class="font-semibold">Currency:</p>
                    <p class="text-indigo-100">Ghana Cedis (GHS)</p>
                </div>
                <div>
                    <p class="font-semibold">Framework:</p>
                    <p class="text-indigo-100">Laravel 10+</p>
                </div>
                <div>
                    <p class="font-semibold">Security:</p>
                    <p class="text-indigo-100">UUID-based route binding</p>
                </div>
            </div>
        </div>

        <!-- Support Section -->
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Need Help?</h2>
            <p class="text-gray-700 mb-4">This documentation covers all administrator features currently implemented in the system.</p>
            <p class="text-gray-600 text-sm">Features for Manager, Staff, and Customer roles will be added as they are implemented.</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>&copy; {{ date('Y') }} Livestock Management System. All rights reserved.</p>
            <p class="text-gray-400 text-sm mt-2">Administrator User Guide - Version 1.0</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
