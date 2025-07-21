<?php
include 'db.php'; // Your database connection file

// Fetch all sales data
// This query groups by sale ID to get total items and total cost for each sale
$sql = "SELECT s.id, s.customer_name, s.order_date, SUM(si.quantity) AS total_items, SUM(si.subtotal) AS total_cost
        FROM sales s
        JOIN sale_items si ON s.id = si.sale_id
        GROUP BY s.id, s.customer_name, s.order_date
        ORDER BY s.order_date DESC"; // Order by most recent orders first

$result = $conn->query($sql);

$grand_total_overall = 0;
// Store all rows in an array to iterate twice (once for total, once for display)
$all_orders = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $all_orders[] = $row;
        $grand_total_overall += $row['total_cost'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 8px 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
            border-radius: 4px;
        }
        .main {
            flex-grow: 1;
            padding: 30px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>🎁 CA MOTO GIFT SHOP & FOOD STOP</h4>
        <hr>
        <a href="index.php">🏠 Dashboard</a>
        <a href="order.php">🛒 Place Order</a>
        <a href="sales_today.php">📊 Sales Today</a>
        <a href="view_order.php">📋 All Orders</a> <a href="add.php">➕ Add Product</a>
    </div>

    <div class="main">
        <h2 class="mb-4">All Orders Report</h2>

        <div class="mb-3">
            <button id="deleteAllOrdersBtn" class="btn btn-danger">🗑️ Delete All Orders</button>
            <small class="text-danger ms-2">Warning: This will permanently delete all sales data. Product stock will NOT be replenished automatically.</small>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total Items</th>
                    <th>Total Cost</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($all_orders)) {
                    foreach ($all_orders as $row) {
                        // Format the date to include time for display
                        $display_date = date('Y-m-d h:i A', strtotime($row['order_date']));
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>" . htmlspecialchars($row['customer_name']) . "</td>
                                <td>" . htmlspecialchars($display_date) . "</td>
                                <td>{$row['total_items']}</td>
                                <td>₱" . number_format($row['total_cost'], 2) . "</td>
                                <td>
                                    <a href='order_details.php?id={$row['id']}' class='btn btn-sm btn-info'>View</a>
                                    <a href='edit_order.php?id={$row['id']}' class='btn btn-sm btn-warning'>Edit</a>
                                    <a href='delete_order.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this order? This will also return stock to inventory.\")'>Delete</a>
                                    <button class='btn btn-sm btn-secondary'>Print</button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo '<tr><td colspan="6">No orders found.</td></tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Overall Grand Total</th>
                    <th>₱<?= number_format($grand_total_overall, 2) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
        document.getElementById('deleteAllOrdersBtn').addEventListener('click', function() {
            if (confirm('ARE YOU ABSOLUTELY SURE YOU WANT TO DELETE ALL ORDERS? This action is irreversible and will clear all sales history!')) {
                if (confirm('THIS IS YOUR FINAL WARNING! All sales data will be permanently deleted. Click OK to proceed.')) {
                    window.location.href = 'delete_all_orders.php';
                }
            }
        });
    </script>
</body>
</html>