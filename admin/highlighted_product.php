<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/connection.php';

// Function to set a flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Handle adding a new highlighted product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $product_id = $_POST['product_id'];

    // Check if the product is already highlighted
    $check_sql = "SELECT * FROM highlighted_products WHERE product_id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$product_id]);

    if ($check_stmt->rowCount() == 0) {
        // Insert highlighted product into the highlighted_products table
        $insert_sql = "INSERT INTO highlighted_products (product_id) VALUES (?)";
        $insert_stmt = $pdo->prepare($insert_sql);

        if ($insert_stmt->execute([$product_id])) {
            setFlashMessage('success', "Product successfully highlighted.");
        } else {
            setFlashMessage('error', "Error: Could not add highlighted product.");
        }
    } else {
        setFlashMessage('error', "This product is already highlighted.");
    }
    
    // Redirect to prevent form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle removing a highlighted product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_product'])) {
    $product_id = $_POST['product_id'];

    // Remove highlighted product from the highlighted_products table
    $remove_sql = "DELETE FROM highlighted_products WHERE product_id = ?";
    $remove_stmt = $pdo->prepare($remove_sql);

    if ($remove_stmt->execute([$product_id])) {
        setFlashMessage('success', "Product successfully removed from highlights.");
    } else {
        setFlashMessage('error', "Error: Could not remove highlighted product.");
    }
    
    // Redirect to prevent form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch all products for selection
$all_products_sql = "SELECT * FROM products";
$all_products = $pdo->query($all_products_sql)->fetchAll();

// Fetch all highlighted products
$highlighted_sql = "SELECT p.id, p.name, p.description, p.price 
                    FROM products p 
                    JOIN highlighted_products hp ON p.id = hp.product_id";
$highlighted_products = $pdo->query($highlighted_sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Highlighted Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>
            <div class="content col-md-9 ms-sm-auto col-lg-10 px-4">
                <h2 class="mt-4 mb-4">Manage Highlighted Products</h2>

                <?php
                // Display flash message if it exists
                if (isset($_SESSION['flash_message'])) {
                    $message = $_SESSION['flash_message'];
                    $alertClass = $message['type'] === 'success' ? 'alert-success' : 'alert-danger';
                    echo "<div class='alert {$alertClass}' role='alert'>{$message['message']}</div>";
                    // Clear the flash message
                    unset($_SESSION['flash_message']);
                }
                ?>

                <!-- Add Highlighted Product Form -->
                <form method="POST" action="" class="mb-4">
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Select Product to Highlight</label>
                        <select class="form-select" id="product_id" name="product_id" required>
                            <option value="">Select a product</option>
                            <?php foreach ($all_products as $product): ?>
                                <option value="<?= $product['id']; ?>"><?= htmlspecialchars($product['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-primary">Add Highlighted Product</button>
                </form>

                <!-- Display Highlighted Products -->
                <h3 class="mb-3">Current Highlighted Products</h3>
                <div class="row">
                    <?php foreach ($highlighted_products as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($product['description']); ?></p>
                                    <p class="card-text">Price: $<?= number_format($product['price'], 2); ?></p>
                                    <form method="POST" action="" onsubmit="return confirmRemove('<?= htmlspecialchars($product['name']); ?>');">
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <button type="submit" name="remove_product" class="btn btn-danger">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <a href="products.php" class="btn btn-secondary mt-3">Back to Product List</a>
            </div>
        </div>
    </div>

    <script>
    function confirmRemove(productName) {
        return confirm(`Are you sure you want to remove "${productName}" from highlighted products?`);
    }
    </script>
</body>

</html>