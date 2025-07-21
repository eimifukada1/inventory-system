<?php
include 'db.php';

$sale_id = $_POST['sale_id'];
$customer = $_POST['customer_name'];
$conn->query("UPDATE sales SET customer_name = '$customer' WHERE id = $sale_id");

// Update items
$item_ids = $_POST['item_id'];
$product_ids = $_POST['product_id'];
$quantities = $_POST['quantity'];

for ($i = 0; $i < count($item_ids); $i++) {
    $product_id = $product_ids[$i];
    $quantity = $quantities[$i];
    $price = $conn->query("SELECT price FROM products WHERE id = $product_id")->fetch_assoc()['price'];
    $total = $price * $quantity;
    
    $conn->query("UPDATE sale_items SET product_id = $product_id, quantity = $quantity, subtotal = $total WHERE id = " . $item_ids[$i]);
}

header("Location: view_order.php?id=$sale_id");
