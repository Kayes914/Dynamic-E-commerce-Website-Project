<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/connection.php';

// Handle product deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
}

// Fetch product data
$sql = "SELECT * FROM products";
$products = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="content col-md-9 ms-sm-auto col-lg-10 px-4">
                <h2 class="mt-4 mb-4">Manage Products</h2>

                <!-- Product List -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Product List</h5>
                        <div>
                            <a href="add_product.php" class="btn btn-success btn-add">Add Product</a>
                            <a href="highlighted_product.php" class="btn btn-info btn-add">Add Highlighted Product</a>
                        </div>
                    </div>
                    <div class="card-body table-container">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <!-- Display the product image -->
                                        <td>
                                            <?php if (!empty($product['image'])): ?>
                                                <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" width="60">
                                            <?php else: ?>
                                                <img src="../uploads/default.jpg" alt="No Image" width="60">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']); ?></td>
                                        <td><?= htmlspecialchars($product['description']); ?></td>
                                        <td>$<?= number_format($product['price'], 2); ?></td>
                                        <td><?= htmlspecialchars($product['stock']); ?></td>
                                        <td>
                                            <form method="POST" action="products.php" style="display:inline;">
                                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" name="delete_product"
                                                    onclick="return confirm('Are you sure you want to delete this product?');">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php include '../includes/footer.php'; ?>
