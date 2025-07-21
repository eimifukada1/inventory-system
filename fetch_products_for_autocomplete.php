<?php
include 'db.php'; // Your database connection file

header('Content-Type: application/json'); // Tell the browser to expect JSON

$suggestions = []; // Array to hold our product suggestions

if (isset($_GET['term'])) {
    $search_term = $conn->real_escape_string($_GET['term']);

    // Query to find active products matching the search term in name or category
    $sql = "SELECT id, name, category, price FROM products
            WHERE is_active = 1
            AND (name LIKE '%$search_term%' OR category LIKE '%$search_term%')
            LIMIT 10"; // Limit the number of suggestions to improve performance

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // 'label' is what jQuery UI Autocomplete displays in the dropdown.
            // 'value' is what jQuery UI Autocomplete puts into the input field by default.
            // We use 'name' for 'value' here, and also pass other data.
            $suggestions[] = [
                'label' => htmlspecialchars($row['name']) . " (Category: " . htmlspecialchars($row['category']) . ", ₱" . number_format($row['price'], 2) . ")",
                'value' => htmlspecialchars($row['name']), // This is the value that is put into the input if selected
                'id' => $row['id'],
                'name' => htmlspecialchars($row['name']), // Pass the actual name for custom rendering
                'category' => htmlspecialchars($row['category']),
                'price' => $row['price']
            ];
        }
    }
}

echo json_encode($suggestions); // Output the suggestions as JSON
?>