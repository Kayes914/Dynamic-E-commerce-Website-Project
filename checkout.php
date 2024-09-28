<?php
include 'includes/connection.php';
include 'includes/header.php';

// Check if the cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Fetch product details for items in the cart
$cartItems = [];
$cartTotal = 0;

$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));

$stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id IN ($placeholders)");
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
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Existing code for processing the order
    $paymentMethod = $_POST['payment_method'];
    $transactionId = null;
    $paymentPhone = null;

    if ($paymentMethod === 'bkash_nagad') {
        $transactionId = $_POST['transaction_id'];
        $paymentPhone = $_POST['payment_phone'];

        // Validate these fields
        if (empty($transactionId) || empty($paymentPhone)) {
            $_SESSION['error'] = "Transaction ID and Payment Phone are required for Bkash/Nagad payments.";
            header('Location: checkout.php');
            exit;
        }
    }

    // Get the full name, address, and phone number from the form
    $fullName = $_POST['name'];
    $address = $_POST['address'];
    $phoneNumber = $_POST['phone_number'];

    // Validate the full name, address, and phone number fields
    if (empty($fullName) || empty($address) || empty($phoneNumber)) {
        $_SESSION['error'] = "Full Name, Address, and Phone Number are required.";
        header('Location: checkout.php');
        exit;
    }

    // Update your database query to include full_name, address, and phone_number fields
    $orderQuery = "INSERT INTO orders (user_id, total_amount, status, payment_method, transaction_id, payment_phone, full_name, address, phone_number, created_at) 
                   VALUES (?, ?, 'pending', ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($orderQuery);
    $stmt->execute([$_SESSION['user_id'], $cartTotal, $paymentMethod, $transactionId, $paymentPhone, $fullName, $address, $phoneNumber]);

    // Clear the cart
    unset($_SESSION['cart']);

    // Set success message
    $_SESSION['success'] = "Your order has been placed successfully!";

    // Redirect to the same page to show the success message
    header('Location: checkout.php');
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Checkout</h1>

        <!-- Display success message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']) ?>
            </div>
            <?php unset($_SESSION['success']); // Remove success message after displaying ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <h2>Order Summary</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td>$<?= number_format($item['price'], 2) ?></td>
                                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Total:</th>
                            <th>$<?= number_format($cartTotal, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col-md-6">
                <h2>Shipping and Payment Information</h2>
                <form method="POST" action="checkout.php" id="checkoutForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                            <option value="bkash_nagad">Bkash/Nagad</option>
                        </select>
                    </div>
                    <div id="bkash_nagad_form" style="display: none;">
                        <h3 class="mt-4">Bkash/Nagad Payment Details</h3>
                        <div class="form-group">
                            <label for="transaction_id">Transaction ID</label>
                            <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                        </div>
                        <div class="form-group">
                            <label for="payment_phone">Payment Phone Number</label>
                            <input type="tel" class="form-control" id="payment_phone" name="payment_phone">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var paymentMethod = document.getElementById('payment_method');
            var bkashNagadForm = document.getElementById('bkash_nagad_form');
            var checkoutForm = document.getElementById('checkoutForm');
            var transactionId = document.getElementById('transaction_id');
            var paymentPhone = document.getElementById('payment_phone');

            paymentMethod.addEventListener('change', function() {
                if (this.value === 'bkash_nagad') {
                    bkashNagadForm.style.display = 'block';
                } else {
                    bkashNagadForm.style.display = 'none';
                }
            });

            checkoutForm.addEventListener('submit', function(e) {
                if (paymentMethod.value === 'bkash_nagad') {
                    if (transactionId.value.trim() === '' || paymentPhone.value.trim() === '') {
                        e.preventDefault();
                        alert('Please fill in both Transaction ID and Payment Phone Number for Bkash/Nagad payments.');
                    }
                }
            });
        });
    </script>
</body>
</html>
