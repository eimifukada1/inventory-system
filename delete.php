<?php
include 'db.php';

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);

    // Perform a soft delete: Update 'is_active' to 0 instead of deleting the row
    $sql = "UPDATE products SET is_active = 0 WHERE id = $product_id";

    if ($conn->query($sql) === TRUE) {
        echo "Product marked as inactive successfully.";
    } else {
        echo "Error marking product as inactive: " . $conn->error;
    }
} else {
    echo "No product ID provided.";
}

// Redirect back to the dashboard or product list
header('Location: index.php');
exit();
?>