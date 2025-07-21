<?php
include 'db.php';

$product = null; // Initialize product to null

// Check if a product ID is provided in the URL for fetching data
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch existing product data to pre-fill the form
    $result = $conn->query("SELECT * FROM products WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("❌ Product not found or invalid ID.");
    }
} elseif (isset($_POST['id'])) {
    // This block handles the form submission (POST request)
    $id = intval($_POST['id']); // Get ID from hidden input field

    // Retrieve updated data from the form
    $name = $conn->real_escape_string($_POST['name']);
    $category = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $cost_price = floatval($_POST['cost_price']);
    $stock = intval($_POST['stock']);

    // Update the product in the database
    $sql = "UPDATE products SET
                name = '$name',
                category = '$category',
                price = $price,
                cost_price = $cost_price,
                stock = $stock
            WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        // Redirect back to dashboard after successful update
        header('Location: index.php');
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    // If no ID is provided in GET or POST, display an error or redirect
    die("❌ No product ID provided for editing.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        <h2 class="mb-4">Edit Product</h2>

        <?php if ($product): // Only show the form if product data was fetched ?>
        <form action="edit.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">

            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <input type="text" name="category" id="category" class="form-control" value="<?= htmlspecialchars($product['category']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Selling Price (₱)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="cost_price" class="form-label">Cost Price (₱)</label>
                <input type="number" step="0.01" name="cost_price" id="cost_price" class="form-control" value="<?= htmlspecialchars($product['cost_price']) ?? 0.00 ?>" required>
                <small class="form-text text-muted">The cost at which you acquire the product. This affects 'Capital'.</small>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Stock</label>
                <input type="number" name="stock" id="stock" class="form-control" value="<?= htmlspecialchars($product['stock']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Product</button>
            <button type="button" class="btn btn-outline-secondary ms-2" onclick="history.back()">↩️ Go Back</button>
            <a href="index.php" class="btn btn-outline-primary ms-2">🏠 Back to Dashboard</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>