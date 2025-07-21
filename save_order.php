<?php
include 'db.php';

$customer_name = $_POST['customer_name'];
$product_ids = $_POST['product_id'];
$quantities = $_POST['quantity'];
$order_date = date('Y-m-d');

// Insert into sales
$stmt = $conn->prepare("INSERT INTO sales (customer_name, order_date) VALUES (?, ?)");
$stmt->bind_param("ss", $customer_name, $order_date);
$stmt->execute();
$sale_id = $conn->insert_id;

// Insert each item
for ($i = 0; $i < count($product_ids); $i++) {
    $product_id = $product_ids[$i];
    $quantity = $quantities[$i];

    // Get price
    $product = $conn->query("SELECT price FROM products WHERE id = $product_id")->fetch_assoc();
    $price = $product['price'];
    $total = $price * $quantity;

    $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $sale_id, $product_id, $quantity, $total);
    $stmt->execute();
}

header("Location: sales_today.php");
?>
