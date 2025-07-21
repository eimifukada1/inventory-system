<?php
include 'db.php'; // Your database connection file

$message = ''; // To store success or error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input
    // Using mysqli_real_escape_string for basic sanitation (important without prepared statements)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']); // Ensure price is a float
    $stock = intval($_POST['stock']);   // Ensure stock is an integer
    $cost_price = floatval($_POST['cost_price']); // Ensure cost_price is a float

    // Basic validation
    if (empty($name) || empty($category) || $price <= 0 || $stock < 0 || $cost_price <= 0) {
        $message = '<div class="alert error">Please fill all fields correctly. Price and Cost Price must be greater than 0. Stock cannot be negative.</div>';
    } else {
        // Using prepared statements for security (recommended even without Bootstrap)
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, stock, cost_price, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        if ($stmt) {
            $stmt->bind_param("ssddi", $name, $category, $price, $stock, $cost_price); // s=string, d=double, i=integer
            if ($stmt->execute()) {
                $message = '<div class="alert success">Product added successfully!</div>';
            } else {
                $message = '<div class="alert error">Error: ' . htmlspecialchars($stmt->error) . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert error">Error preparing statement: ' . htmlspecialchars($conn->error) . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <style>
        /* General Body and Layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa; /* Light background */
            display: flex; /* Use flexbox for sidebar and main content */
            min-height: 100vh; /* Full viewport height */
        }

        /* Sidebar Styling */
        .sidebar {
            width: 240px;
            background-color: #343a40; /* Dark gray */
            color: white;
            padding: 20px;
            box-sizing: border-box; /* Include padding in width */
            position: fixed; /* Keep sidebar fixed */
            height: 100%; /* Full height */
            overflow-y: auto; /* Enable scrolling if content is long */
        }
        .sidebar h4 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #ffffff;
        }
        .sidebar hr {
            border: 0;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.2); /* Lighter line */
            margin: 15px 0;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #495057; /* Lighter gray on hover */
        }

        /* Main Content Area */
        .main {
            margin-left: 240px; /* Offset by sidebar width */
            flex-grow: 1; /* Take remaining space */
            padding: 30px;
            box-sizing: border-box;
        }
        .main h2 {
            margin-top: 0;
            margin-bottom: 25px;
            color: #333;
        }

        /* Card-like container for the form */
        .card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); /* Subtle shadow */
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 20px; /* Spacing between form fields */
        }
        .form-group label {
            display: block; /* Make labels appear above inputs */
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select {
            width: calc(100% - 20px); /* Full width minus padding */
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box; /* Include padding in width */
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group select:focus {
            border-color: #007bff; /* Highlight on focus */
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); /* Subtle glow */
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none; /* For anchor tags styled as buttons */
            display: inline-block; /* Allow side-by-side */
            margin-right: 10px; /* Spacing between buttons */
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff; /* Blue */
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .btn-secondary {
            background-color: #6c757d; /* Gray */
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62; /* Darker gray on hover */
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
        }
        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
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
        <h2>➕ Add New Product</h2>

        <?= $message ?> <div class="card">
            <form action="add.php" method="POST">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Gift">Gift</option>
                        <option value="Food">Food</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Souvenir">Souvenir</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price (₱):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="cost_price">Cost Price (₱):</label>
                    <input type="number" id="cost_price" name="cost_price" step="0.01" min="0.01" required>
                </div>

                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>

                <button type="submit" class="btn btn-primary">💾 Save Product</button>
                <a href="index.php" class="btn btn-secondary">↩️ Back to Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>