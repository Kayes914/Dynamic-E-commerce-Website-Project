<?php
require_once 'includes/connection.php'; // Adjust this to your database connection file
include 'includes/header.php';

// Handle AJAX request to add items to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update the quantity of the product in the cart
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }

    // Check if the request is made via AJAX and respond with JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);
        exit();
    }

    // Fallback: if it's not an AJAX request, redirect to the cart page as usual
    header('Location: cart.php');
    exit();
}

// Remove item from cart
if (isset($_POST['remove_id'])) {
    $removeId = $_POST['remove_id'];
    unset($_SESSION['cart'][$removeId]);
    echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']);
    exit();
}

// Update quantity in the cart
if (isset($_POST['update_id']) && isset($_POST['quantity'])) {
    $updateId = $_POST['update_id'];
    $quantity = intval($_POST['quantity']);

    if ($quantity <= 0) {
        unset($_SESSION['cart'][$updateId]);
    } else {
        $_SESSION['cart'][$updateId] = $quantity;
    }

    echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
    exit();
}

// Fetch product details for items in the cart
$cartTotal = 0;
$cartItems = [];
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    $stmt = $pdo->prepare("SELECT id, name, price, image FROM products WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $cartTotal += $subtotal;

        $cartItems[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Your Shopping Cart</h1>
        <div id="cart-message" class="alert" style="display:none;"></div>
        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <tr id="item-<?= $item['id'] ?>">
                            <td>
                                <?php if (!empty($item['image']) && file_exists('uploads/' . $item['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/50" alt="Placeholder" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <input type="number" class="form-control quantity-input" data-id="<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" min="0">
                            </td>
                            <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-id="<?= $item['id'] ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total:</th>
                        <th id="cart-total">$<?= number_format($cartTotal, 2) ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
            <div class="text-right">
                <button id="update-quantities" class="btn btn-primary">Update Quantities</button>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Continue Shopping</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).on('click', '.remove-item-btn', function() {
            var itemId = $(this).data('id');
            // Confirmation prompt
            if (confirm("Are you sure you want to remove this item from your cart?")) {
                $.ajax({
                    type: 'POST',
                    url: 'cart.php',
                    data: { remove_id: itemId },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#item-' + itemId).remove();
                            $('#cart-message').addClass('alert-success').text(result.message).show();
                        }
                    }
                });
            }
        });

        $('#update-quantities').on('click', function() {
            $('.quantity-input').each(function() {
                var itemId = $(this).data('id');
                var quantity = $(this).val();

                $.ajax({
                    type: 'POST',
                    url: 'cart.php',
                    data: { update_id: itemId, quantity: quantity },
                    success: function(response) {
                        var result = JSON.parse(response);
                        if (result.status === 'success') {
                            $('#cart-message').addClass('alert-success').text(result.message).show();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
