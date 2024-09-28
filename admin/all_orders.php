<?php
// Include the database connection file
include('../includes/connection.php'); // Adjust the path to your database connection file

// Check if a status update is requested
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['status'];

    // Update the status of the selected order
    $updateQuery = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([$newStatus, $orderId]);

    // Redirect to prevent form resubmission
    header('Location: all_orders.php');
    exit;
}

// Query to fetch all orders with additional fields
$query = "SELECT o.id, o.full_name, o.total_amount, o.status, o.created_at, 
                 o.phone_number, o.address, o.transaction_id, o.payment_phone
          FROM orders o
          ORDER BY o.created_at DESC";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/all_orders.css" rel="stylesheet"> <!-- Link to external CSS file -->
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Include Sidebar -->
        <?php include 'sidebar.php'; ?>

        <!-- Main content -->
        <main class="content col-md-9 ms-sm-auto col-lg-10 px-4">
            <h2 class="mt-4 mb-4">All Orders</h2>
            <table class="table table-striped custom-table"> <!-- Added custom class for styling -->
                <thead class="table-header">
                    <tr>
                        <th>Order ID</th>
                        <th>Full Name</th>
                        <th>Total Amount</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Transaction ID</th>
                        <th>Payment Phone</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']); ?></td>
                            <td><?= htmlspecialchars($row['full_name']); ?></td>
                            <td>$<?= number_format($row['total_amount'], 2); ?></td>
                            <td><?= htmlspecialchars($row['phone_number']); ?></td>
                            <td><?= htmlspecialchars($row['address']); ?></td>
                            <td><?= htmlspecialchars($row['transaction_id']); ?></td>
                            <td><?= htmlspecialchars($row['payment_phone']); ?></td>
                            <td><?= ucfirst(htmlspecialchars($row['status'])); ?></td>
                            <td><?= htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <!-- Form to update order status -->
                                <form method="POST" action="all_orders.php">
                                    <input type="hidden" name="order_id" value="<?= $row['id']; ?>">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="completed" <?= $row['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?= $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</div>
</body>
</html>
