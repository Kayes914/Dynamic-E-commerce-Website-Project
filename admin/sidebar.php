<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>
    <!-- sidebar.php -->
<nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
    <div class="sidebar-header">
        <a href="../index.php" class="header-link">
            <h4>Home</h4>
        </a>
    </div>
    <div class="position-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link sidebar-link active" href="dashboard.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link" href="products.php">
                    <i class="bi bi-box"></i> Products
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link" href="all_orders.php">
                    <i class="bi bi-file-earmark-text"></i> Orders
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link sidebar-link" href="users.php">
                    <i class="bi bi-person"></i> Users
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-footer">
        <a href="../logout.php" class="btn btn-outline-light btn-logout">Logout</a>
    </div>
</nav>

</body>
</html>