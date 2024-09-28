<?php
include 'includes/connection.php';
include 'includes/header.php';

// Pagination settings
$productsPerPage = 8;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $productsPerPage;

// Fetch products with pagination
$sql = "SELECT * FROM products LIMIT :offset, :limit";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $productsPerPage, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Get total product count for pagination
$totalProductsSql = "SELECT COUNT(*) FROM products";
$totalProducts = $pdo->query($totalProductsSql)->fetchColumn();
$totalPages = ceil($totalProducts / $productsPerPage);

// Fetch the highlighted products
$highlightSql = "SELECT p.* FROM highlighted_products hp JOIN products p ON hp.product_id = p.id WHERE hp.featured = 1 LIMIT 3";
$highlightStmt = $pdo->prepare($highlightSql);
$highlightStmt->execute();
$highlightedProducts = $highlightStmt->fetchAll();

// Check if there's a success message
if (isset($_SESSION['order_success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['order_success'] . '</div>';
    unset($_SESSION['order_success']); // Clear the message after displaying it
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finch Fashion - Stylish Clothing for Every Occasion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
</head>
<body>
 <main>
 <section id="home" class="hero d-flex align-items-center justify-content-center text-center text-white">
    <div class="hero-content" data-aos="fade-up">
        <h1 class="display-3 fw-bold">Discover Your Style</h1>
        <p class="lead">Explore our latest collection of trendy and affordable fashion</p>
        <a href="#products" class="btn btn-primary btn-lg mt-3">Shop Now</a>
    </div>
</section>

<section id="featured" class="featured-products py-5">
    <div class="container">
        <h2 class="text-center mb-5 featured-heading" data-aos="fade-up" data-aos-duration="1000">Featured Products</h2>
        <div class="row">
            <?php foreach ($highlightedProducts as $product): ?>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="<?= $loop * 100 ?>">
                    <div class="card shadow-sm border-0 h-100 featured-card">
                        <?php
                        $imagePath = htmlspecialchars($product['image']);
                        if (!empty($imagePath) && file_exists('uploads/' . $imagePath)):
                            ?>
                            <img src="uploads/<?= $imagePath ?>" class="card-img-top img-fluid" style="max-height: 250px; object-fit: cover;" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200" alt="Placeholder" class="card-img-top img-fluid" style="max-height: 250px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-center"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text text-muted text-center flex-grow-1">
                                <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...
                            </p>
                            <p class="card-text text-center"><strong>$<?= number_format($product['price'], 2) ?></strong></p>
                            <div class="mt-auto">
                                <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary mb-2 w-100">View Details</a>
                                <form class="add-to-cart-form" method="POST">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="btn btn-primary w-100 buy-now-btn">Buy Now</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<section id="products" class="products py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5 our-products" data-aos="fade-up">Our Products</h2>
        <div class="row">
            <?php foreach ($products as $index => $product): ?>
                <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="<?= $index * 50 ?>">
                    <div class="card product-card h-100">
                        <?php
                        $imagePath = htmlspecialchars($product['image']);
                        if (!empty($imagePath) && file_exists('uploads/' . $imagePath)):
                            ?>
                            <img src="uploads/<?= $imagePath ?>" class="card-img-top"
                                alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/300x200" alt="Placeholder" class="card-img-top">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="card-text flex-grow-1">
                                <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...
                            </p>
                            <p class="card-text"><strong>$<?= number_format($product['price'], 2) ?></strong></p>
                            <a href="product_details.php?id=<?= $product['id'] ?>" class="btn btn-outline-primary mt-auto">View Details</a>
                            
                            <!-- Buy Now Button -->
                            <form class="add-to-cart-form mt-2" method="POST">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="button" class="btn btn-primary w-100 buy-now-btn">Buy Now</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


        <nav aria-label="Product navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </main>
<!-- Add this script at the bottom of your page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach event listener to all 'Buy Now' buttons
        const buyNowButtons = document.querySelectorAll('.buy-now-btn');
        
        buyNowButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                
                const form = this.closest('form');
                const formData = new FormData(form);
                
                // Send an AJAX request to add the item to the cart
                fetch('cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Product added to cart successfully!');
                        // Optionally, update the cart icon or display a message
                    } else {
                        alert('Error adding product to cart!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>
    <script>
</body>

</html>

<?php include 'includes/footer.php'; ?>