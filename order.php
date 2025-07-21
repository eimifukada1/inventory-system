<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Place Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h2 class="mb-4">New Customer Order</h2>
    <form method="POST" action="save_order.php">
        <div class="mb-3">
            <label>Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>

        <div id="order-items">
            <div class="row mb-3 order-item">
                <div class="col-md-5">
                    <select name="product_id[]" class="form-select" required>
                        <option value="">Select Product</option>
                        <?php
                        $products = $conn->query("SELECT id, name, price FROM products WHERE is_active = 1");
                        while ($row = $products->fetch_assoc()):
                        ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?> (₱<?= number_format($row['price'], 2) ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" min="1" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-secondary mb-3" id="add-more">Add More</button>
        <br>
        <button type="submit" class="btn btn-primary">Submit Order</button>
        <button type="button" class="btn btn-outline-secondary ms-2" onclick="history.back()">↩️ Go Back</button>
        <a href="index.php" class="btn btn-outline-primary ms-2">🏠 Back to Dashboard</a>
    </form>
</div>

<script>
document.getElementById('add-more').addEventListener('click', function () {
    const item = document.querySelector('.order-item');
    const clone = item.cloneNode(true);
    clone.querySelectorAll('input').forEach(input => input.value = '');
    document.getElementById('order-items').appendChild(clone);
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-item')) {
        const items = document.querySelectorAll('.order-item');
        if (items.length > 1) {
            e.target.closest('.order-item').remove();
        }
    }
});
</script>
</body>
</html>
