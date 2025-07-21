<?php
include 'db.php'; // Your database connection file

$order_id = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $order_id = (int)$_GET['id']; // Cast to integer for security

    // Fetch sale details using prepared statement for security
    $sale_sql = "SELECT id, customer_name, order_date FROM sales WHERE id = ?";
    $stmt_sale = $conn->prepare($sale_sql);
    $stmt_sale->bind_param("i", $order_id);
    $stmt_sale->execute();
    $sale_result = $stmt_sale->get_result();
    $sale_details = $sale_result->fetch_assoc();
    $stmt_sale->close();

    if (!$sale_details) {
        // No sale found with this ID
        $order_id = null; // Set to null to trigger "no order ID" message
    } else {
        // Fetch sale items and JOIN with products for name and price using prepared statement
        $items_sql = "SELECT si.quantity, si.subtotal, p.name AS product_name, p.price AS product_price
                      FROM sale_items si
                      JOIN products p ON si.product_id = p.id
                      WHERE si.sale_id = ?";
        $stmt_items = $conn->prepare($items_sql);
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $items_result = $stmt_items->get_result();
        $sale_items = [];
        while($row = $items_result->fetch_assoc()) {
            $sale_items[] = $row;
        }
        $stmt_items->close();
    }

}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
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
        <?php if ($order_id === null || !$sale_details): ?>
            <div class="alert alert-danger" role="alert">
                ❌ No order ID provided or order not found. Please go back and select an order.
            </div>
            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">↩️ Go Back</button>
            <a href="index.php" class="btn btn-outline-primary ms-2">🏠 Back to Dashboard</a>
        <?php else: ?>
            <h2 class="mb-4">Order Details #<?= htmlspecialchars($sale_details['id']) ?></h2>

            <div class="card mb-4">
                <div class="card-header">
                    Order Information
                </div>
                <div class="card-body">
                    <p><strong>Customer Name:</strong> <?= htmlspecialchars($sale_details['customer_name']) ?></p>
                    <p><strong>Order Date:</strong> <?= date('Y-m-d h:i A', strtotime($sale_details['order_date'])) ?></p>
                </div>
            </div>

            <h4>Order Items</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Product Name</th>
                        <th>Price (per item)</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_order_cost = 0;
                    if (!empty($sale_items)) {
                        foreach ($sale_items as $item) {
                            $total_order_cost += $item['subtotal'];
                            echo "<tr>
                                    <td>" . htmlspecialchars($item['product_name']) . "</td>
                                    <td>₱" . number_format($item['product_price'], 2) . "</td>
                                    <td>{$item['quantity']}</td>
                                    <td>₱" . number_format($item['subtotal'], 2) . "</td>
                                </tr>";
                        }
                    } else {
                        echo '<tr><td colspan="4">No items found for this order.</td></tr>';
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total Order Cost:</th>
                        <th>₱<?= number_format($total_order_cost, 2) ?></th>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4">
                <button type="button" class="btn btn-outline-secondary" onclick="history.back()">↩️ Go Back</button>
                <a href="edit_order.php?id=<?= $order_id ?>" class="btn btn-warning">✏️ Edit Order</a>
                <a href="delete_order.php?id=<?= $order_id ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this order? This will also return stock to inventory.')">🗑️ Delete Order</a>
                <button class="btn btn-secondary">⎙ Print Receipt</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>