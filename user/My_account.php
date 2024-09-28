<?php
require_once '../includes/connection.php';
include '../includes/header.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user details
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT id, username, email, phone_no, address FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Process the form to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_no'];
    $address = $_POST['address'];

    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone_no = ?, address = ? WHERE id = ?");
    $updateStmt->execute([$username, $email, $phone_number, $address, $userId]);

    $_SESSION['success_message'] = 'Account details updated successfully!';
    header('Location: my_account.php');
    exit;
}

// Fetch user order history
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$orderStmt->execute([$userId]);
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/my_account.css">
</head>
<body>
<div class="container">
    <div class="row">
        <!-- Account Details -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Account Information</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?= $_SESSION['success_message']; ?>
                            <?php unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="my_account.php">
    <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required autocomplete="username">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required autocomplete="email">
    </div>
    <div class="mb-3">
        <label for="phone_no" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phone_no" name="phone_no" value="<?= htmlspecialchars($user['phone_no']) ?>" required autocomplete="tel">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required autocomplete="address">
    </div>
    <button type="submit" class="btn btn-primary w-100">Update Details</button>
</form>

                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Order History</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($orders)): ?>
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Items</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['id']); ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2); ?></td>
                                        <td><?= ucfirst(htmlspecialchars($order['status'])); ?></td>
                                        <td>
                                            <?php
                                            // Fetch items in the order
                                            $orderItemsStmt = $pdo->prepare("SELECT p.name, oi.quantity FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                                            $orderItemsStmt->execute([$order['id']]);
                                            $items = $orderItemsStmt->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($items as $item) {
                                                echo htmlspecialchars($item['name']) . " (x" . $item['quantity'] . ")<br>";
                                            }
                                            ?>
                                        </td>
                                        <td><?= htmlspecialchars($order['created_at']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">You have no orders.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
