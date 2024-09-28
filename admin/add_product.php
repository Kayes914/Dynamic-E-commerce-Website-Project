<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Insert product into the database
    $sql = "INSERT INTO products (name, description, price, stock) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$name, $description, $price, $stock])) {
        $productId = $pdo->lastInsertId(); // Get the last inserted product ID

        // Handle file upload
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFiles = 5;
        $uploadDir = '../uploads/'; // Ensure this directory exists and is writable

        $uploadedFiles = []; // Array to store filenames of uploaded images

        if (isset($_FILES['images'])) {
            $totalFiles = count($_FILES['images']['name']);
            if ($totalFiles > $maxFiles) {
                echo "You can only upload up to 5 images.";
                exit();
            }

            for ($i = 0; $i < $totalFiles; $i++) {
                $fileName = $_FILES['images']['name'][$i];
                $fileTmp = $_FILES['images']['tmp_name'][$i];
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                // Validate file extension
                if (!in_array($fileExtension, $allowedExtensions)) {
                    echo "Invalid file type: $fileName";
                    continue;
                }

                // Move the file to the upload directory
                $destination = $uploadDir . basename($fileName);
                if (move_uploaded_file($fileTmp, $destination)) {
                    // Store filename in the array
                    $uploadedFiles[] = $fileName;
                } else {
                    echo "Failed to upload file: $fileName<br>";
                }
            }
        }

        // If there are uploaded files, update the product record with the first image
        if (count($uploadedFiles) > 0) {
            $mainImage = $uploadedFiles[0];
            $sql = "UPDATE products SET image = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$mainImage, $productId]);
        }

        // Redirect to products page
        header("Location: products.php");
        exit();
    } else {
        // Output the SQL error if insertion fails
        echo "Error: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
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
                <h2 class="mt-4 mb-4">Add New Product</h2>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="add_product.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>
                            <div class="mb-3">
                                <label for="images" class="form-label">Product Images (max 5)</label>
                                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                                <small class="form-text text-muted">You can select up to 5 images.</small>
                            </div>
                            <button type="submit" class="btn btn-primary" name="add_product">Add Product</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
