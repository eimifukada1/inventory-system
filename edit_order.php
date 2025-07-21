<?php
include 'db.php';
$id = $_GET['id'];

$sale = $conn->query("SELECT * FROM sales WHERE id = $id")->fetch_assoc();
$items = $conn->query("SELECT * FROM sale_items WHERE sale_id = $id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2>Edit Order #<?= $id ?></h2>
    <form method="POST" action="update_order.php">
        <input type="hidden" name="sale_id" value="<?= $id ?>">
        <div class="mb-3">
            <label>Customer Name</label>
            <input type="text" name="customer_name" value="<?= $sale['customer_name'] ?>" class="form-control" required>
        </div>
        <div id="order-items">
        <?php while ($row = $items->fetch_assoc()): ?>
            <div class="row mb-3 order-item">
                <input type="hidden" name="item_id[]" value="<?= $row['id'] ?>">
                <div class="col-md-5">
                    <select name="product_id[]" class="form-select" required>
                        <?php
                        $products = $conn->query("SELECT * FROM products");
                        while ($p = $products->fetch_assoc()):
                        ?>
                        <option value="<?= $p['id'] ?>" <?= $p['id'] == $row['product_id'] ? 'selected' : '' ?>>
                            <?= $p['name'] ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantity[]" class="form-control" value="<?= $row['quantity'] ?>" required>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <button type="submit" class="btn btn-success">Update Order</button>
    </form>
    <br>
    <div>
         <button type="button" class="btn btn-outline-secondary" onclick="history.back()">↩️ Go Back</button>
        <a href="index.php" class="btn btn-outline-primary ms-2">🏠 Back to Dashboard</a>
                        </br>
</div>
</div>
</body>
</html>
