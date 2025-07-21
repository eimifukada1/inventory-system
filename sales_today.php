<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Today's Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">Sales Report</h2>

    <div class="mb-3 d-flex justify-content-start align-items-center">
    </div>

    <form method="GET" class="row mb-4">
        <div class="col-md-3">
            <input type="date" name="date" value="<?= $_GET['date'] ?? date('Y-m-d') ?>" class="form-control">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
        </form>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Customer</th>
                <th>Date</th>
                <th>Total Items</th>
                <th>Total Cost</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $filter_date = $_GET['date'] ?? date('Y-m-d');
        $sales = $conn->query("SELECT s.id, s.customer_name, s.order_date,
            SUM(si.quantity) AS total_items,
            SUM(si.subtotal) AS total_cost
            FROM sales s
            JOIN sale_items si ON s.id = si.sale_id
            WHERE s.order_date = '$filter_date'
            GROUP BY s.id");

        $grand_total = 0;
        while ($row = $sales->fetch_assoc()):
            $grand_total += $row['total_cost'];
        ?>
            <tr>
                <td><?= $row['customer_name'] ?></td>
                <td><?= $row['order_date'] ?></td>
                <td><?= $row['total_items'] ?></td>
                <td>₱<?= number_format($row['total_cost'], 2) ?></td>
                <td>
                    <a href="order_details.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">View</a>
                    <a href="edit_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_order.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this order?')">Delete</a>
                    <a href="print_receipt.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary" target="_blank">Print</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Grand Total</th>
                <th colspan="2">₱<?= number_format($grand_total, 2) ?></th>
            </tr>
        </tfoot>
        
    </table>
<div>
         <button type="button" class="btn btn-outline-secondary" onclick="history.back()">↩️ Go Back</button>
        <a href="index.php" class="btn btn-outline-primary ms-2">🏠 Back to Dashboard</a>
</div>
</div>
</body>
</html>