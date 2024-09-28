<?php
// Include your database connection file
include 'includes/connection.php'; // Adjust this path according to your structure

// Handle AJAX request to add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    if ($productId > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
        exit;
    }
}

// Get the product ID from the URL
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the product details from the database
if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        // Handle case where product is not found
        echo "<h2 class='text-center'>Product not found</h2>";
        exit;
    }
} else {
    // Handle invalid product ID
    echo "<h2 class='text-center'>Invalid product ID</h2>";
    exit;
}

// Include header after handling AJAX requests
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Product Details</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/product_details.css">
</head>
<body>

<div class="container product-container">
    <div class="row">
        <div class="col-md-6">
            <?php
            $imagePath = htmlspecialchars($product['image']);
            if (!empty($imagePath) && file_exists('uploads/' . $imagePath)):
                ?>
                <img src="uploads/<?= $imagePath ?>" class="img-fluid product-img" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <img src="https://via.placeholder.com/400x400" alt="Placeholder" class="img-fluid product-img">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>
            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
            <p class="product-description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            
            <!-- Add to Cart Button -->
            <button id="buyNowBtn" class="btn btn-primary btn-buy-now w-100" data-product-id="<?= $product['id'] ?>">
                Buy Now <i class="fas fa-shopping-cart"></i>
            </button>
            
            <!-- Cart Message -->
            <div id="cartMessage" class="alert alert-success mt-3" role="alert">
                Product added to cart successfully!
            </div>
        </div>
    </div>
    <div class="text-center back-link">
        <a href="index.php" class="btn btn-outline-secondary">Back to Products</a>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#buyNowBtn').click(function(e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: {
                action: 'add_to_cart',
                product_id: productId,
                quantity: 1
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#cartMessage').fadeIn().delay(3000).fadeOut();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX Error: " + textStatus + ' : ' + errorThrown);
                alert('Error adding product to cart. Please check the console for more details.');
            }
        });
    });
});
</script>
</body>
</html>