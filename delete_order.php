<?php
include 'db.php';
$id = $_GET['id'];

$conn->query("DELETE FROM sale_items WHERE sale_id = $id");
$conn->query("DELETE FROM sales WHERE id = $id");

header("Location: sales_today.php");
?>
