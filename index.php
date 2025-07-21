<?php
include 'db.php';

// Get summary stats (KEEP THESE AS THEY ARE)
$total_products = $conn->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1")->fetch_assoc()['count'];
$total_sales_today_query = $conn->query("
    SELECT SUM(si.subtotal) AS total
    FROM sales s
    JOIN sale_items si ON s.id = si.sale_id
    WHERE DATE(s.order_date) = CURDATE()
");
$total_sales_today = $total_sales_today_query->fetch_assoc()['total'] ?? 0;
$total_orders_today = $conn->query("SELECT COUNT(*) as count FROM sales WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];

$capital_query = $conn->query("SELECT SUM(cost_price * stock) AS total_capital FROM products WHERE is_active = 1");
$total_capital_invested = $capital_query->fetch_assoc()['total_capital'] ?? 0;

$overall_gross_sales_query = $conn->query("SELECT SUM(subtotal) AS overall_gross FROM sale_items");
$overall_gross_sales = $overall_gross_sales_query->fetch_assoc()['overall_gross'] ?? 0;

// --- IMPORTANT: For the main product table, keep the search filter as it was IF you want the table to filter on ENTER key.
// If you only want the autocomplete for quick selection and the table always shows all active products,
// remove the `$search_query` and its `if` block here.
// For this example, let's keep the current table filtering on explicit search submission,
// and add autocomplete on top of it.

$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $conn->real_escape_string($_GET['search']);
}

$product_sql = "SELECT * FROM products WHERE is_active = 1";
if (!empty($search_query)) {
    $product_sql .= " AND (name LIKE '%$search_query%' OR category LIKE '%$search_query%')";
}
$product_sql .= " ORDER BY id ASC";

$result = $conn->query($product_sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Gift Shop Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }
        .sidebar {
            width: 240px;
            background-color: #343a40;
            color: white;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 8px 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
            border-radius: 44px;
        }
        .main {
            flex-grow: 1;
            padding: 30px;
            background-color: #f8f9fa;
        }
        /* Style for jQuery UI Autocomplete dropdown */
        .ui-autocomplete {
            max-height: 200px; /* Limit height */
            overflow-y: auto; /* Add scrollbar if needed */
            overflow-x: hidden;
            z-index: 1000; /* Ensure it appears above other elements */
            border: 1px solid #ced4da; /* Bootstrap-like border */
            border-radius: .25rem;
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        .ui-menu-item .ui-menu-item-wrapper {
            padding: .5rem 1rem;
            color: #212529;
            text-decoration: none;
            background-color: #fff;
            border: 0;
        }
        .ui-menu-item .ui-menu-item-wrapper.ui-state-active {
            color: #fff;
            background-color: #0d6efd; /* Bootstrap primary color */
            border-color: #0d6efd;
            border-radius: .25rem;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4>🎁 CA MOTO GIFT SHOP & FOOD STOP</h4>
        <hr>
        <a href="index.php">🏠 Dashboard</a>
        <a href="order.php">🛒 Place Order</a>
        <a href="sales_today.php">📊 Sales Today</a>
        <a href="view_order.php">📋 All Orders</a>
        <a href="add.php">➕ Add Product</a>
    </div>

    <div class="main">
        <h2 class="mb-4">Dashboard</h2>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">🧾 Sales Today</h5>
                        <p class="card-text fs-4">₱<?= number_format($total_sales_today, 2) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-primary">
                    <div class="card-body">
                        <h5 class="card-title">📦 Total Products</h5>
                        <p class="card-text fs-4"><?= $total_products ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info">
                    <div class="card-body">
                        <h5 class="card-title">🛍️ Orders Today</h5>
                        <p class="card-text fs-4"><?= $total_orders_today ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5 class="card-title">💰 Total Capital in Stock</h5>
                        <p class="card-text fs-4">₱<?= number_format($total_capital_invested, 2) ?></p>
                        <small class="text-muted">Cost of all products currently in stock.</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-body">
                        <h5 class="card-title">📈 Overall Gross Sales</h5>
                        <p class="card-text fs-4">₱<?= number_format($overall_gross_sales, 2) ?></p>
                        <small class="text-muted">Total revenue from all sales ever recorded.</small>
                    </div>
                </div>
            </div>
        </div>


        <h4>📋 Product Inventory</h4>

        <form method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by product name or category..." name="search" id="product_search_input" value="<?= htmlspecialchars($search_query) ?>">
                <button class="btn btn-outline-secondary" type="submit">🔍 Search</button>
                <?php if (!empty($search_query)): ?>
                    <a href="index.php" class="btn btn-outline-danger">Clear</a>
                <?php endif; ?>
            </div>
        </form>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Cost Price</th>
                    <th>Stock</th>
                    <th>Capital (This Item)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $item_capital = $row['cost_price'] * $row['stock'];
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <td>" . htmlspecialchars($row['category']) . "</td>
                                <td>₱" . number_format($row['price'], 2) . "</td>
                                <td>₱" . number_format($row['cost_price'], 2) . "</td>
                                <td>{$row['stock']}</td>
                                <td>₱" . number_format($item_capital, 2) . "</td>
                                <td>
                                    <a href='edit.php?id={$row['id']}' class='btn btn-sm btn-warning'>✏️ Edit</a>
                                    <a href='delete.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Delete this product? This will make it inactive.\")'>🗑️ Delete</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo '<tr><td colspan="8">No products found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
    $(function() {
        $("#product_search_input").autocomplete({
            source: "fetch_products_for_autocomplete.php", // PHP file to fetch suggestions
            minLength: 1, // Start suggesting after 1 character
            select: function(event, ui) {
                // Option 1: Redirect to index.php with the selected product's name
                window.location.href = 'index.php?search=' + encodeURIComponent(ui.item.name);

                // Option 2 (more advanced): Use AJAX to re-fetch and re-render the table directly
                // This requires more complex JS to update the table HTML, but provides a smoother UX.
                // For simplicity, redirecting is easier to start.
                // If you uncomment Option 2, make sure to comment out Option 1
                return false; // Prevent jQuery UI from populating the value itself
            }
        }).autocomplete( "instance" )._renderItem = function( ul, item ) {
            // This function customizes how each item in the dropdown is rendered
            return $( "<li>" )
                .append( "<div><strong>" + item.name + "</strong><br><small class='text-muted'>" + item.category + " - ₱" + parseFloat(item.price).toFixed(2) + "</small></div>" )
                .appendTo( ul );
        };
    });
    </script>
</body>
</html>