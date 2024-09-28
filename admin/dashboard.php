<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include '../includes/connection.php';

// Fetch dashboard statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_income = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'completed'")->fetchColumn();
$daily_sales = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE() AND status = 'completed'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Include Sidebar -->
            <?php include 'sidebar.php'; ?>

            <!-- Main content -->
            <main class="content col-md-9 ms-sm-auto col-lg-10 px-4">
                <h2 class="mt-4 mb-4">Admin Dashboard</h2>

                <!-- Dashboard Stats -->
                <div class="row mb-4">
                    <!-- Total Users -->
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded bg-primary text-white">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-person fs-3 me-3"></i>
                                <div>
                                    <h5 class="card-title mb-1">Total Users</h5>
                                    <p class="card-text fs-4 mb-0"><?= $total_users; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Orders -->
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded bg-success text-white">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-cart-check fs-3 me-3"></i>
                                <div>
                                    <h5 class="card-title mb-1">Total Orders</h5>
                                    <p class="card-text fs-4 mb-0"><?= $total_orders; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total Income -->
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded bg-warning text-dark">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-cash fs-3 me-3"></i>
                                <div>
                                    <h5 class="card-title mb-1">Total Income</h5>
                                    <p class="card-text fs-4 mb-0">$<?= number_format($total_income, 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daily Sales -->
                    <div class="col-md-3 mb-3">
                        <div class="card shadow-sm border-0 rounded bg-danger text-white">
                            <div class="card-body d-flex align-items-center">
                                <i class="bi bi-calendar-month fs-3 me-3"></i>
                                <div>
                                    <h5 class="card-title mb-1">Daily Sales</h5>
                                    <p class="card-text fs-4 mb-0">$<?= number_format($daily_sales, 2); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include '../includes/footer.php'; ?>
