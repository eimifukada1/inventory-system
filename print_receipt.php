<?php
include 'db.php';
$id = $_GET['id'];

$sale = $conn->query("SELECT * FROM sales WHERE id = $id")->fetch_assoc();
$items = $conn->query("SELECT si.*, p.name, p.price 
    FROM sale_items si 
    JOIN products p ON si.product_id = p.id 
    WHERE si.sale_id = $id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Receipt</title>
    <style>
        body { font-family: monospace; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 5px; border-bottom: 1px dashed #999; }
    </style>
</head>
<body onload="window.print()">
    <h2>Gift Shop Receipt</h2>
    <p>Date: <?= $sale['order_date'] ?><br>
    Customer: <?= $sale['customer_name'] ?></p>
    
    <table>
        <thead>
            <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
        </thead>
        <tbody>
        <?php $total = 0;
        while ($row = $items->fetch_assoc()):
            $subtotal = $row['subtotal'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="3">Total</th><th>₱<?= number_format($total, 2) ?></th></tr>
        </tfoot>
    </table>
</body>
</html>
