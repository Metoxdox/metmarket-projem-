<?php
require 'db.php';

// Handle Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    // If not logged in, redirect to login
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $p_id = intval($_POST['product_id']);
    $u_id = $_SESSION['user_id'];
    
    // Check if product is already in cart
    $check = $conn->query("SELECT * FROM cart WHERE user_id=$u_id AND product_id=$p_id");
    
    if($check->num_rows > 0){
        // Update quantity
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$u_id AND product_id=$p_id");
    } else {
        // Insert new item
        $conn->query("INSERT INTO cart (user_id, product_id) VALUES ($u_id, $p_id)");
    }
    
    // Show a quick alert and refresh to prevent form resubmission
    echo "<script>alert('Product added to cart!'); window.location.href='index.php';</script>";
}

// Fetch Products from Database
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Grocery Market</title>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">🍊 Grocery Market</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Admin Panel</a>
                <?php else: ?>
                    <a href="profile.php">My Profile</a>
                    <a href="cart.php">Cart 🛒</a>
                <?php endif; ?>
                
                <a href="logout.php" style="color:#dc3545;">Logout</a>

            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h2>Fresh Arrivals</h2>
        <br>
        <div class="product-grid">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?php echo escape($row['image_url']); ?>" alt="Product">
                    
                    <div class="card-body">
                        <h3><?php echo escape($row['name']); ?></h3>
                        <p style="font-size:0.9rem; color:#666; height: 40px; overflow: hidden;"><?php echo escape($row['description']); ?></p>
                        <br>
                        
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="price">₺<?php echo number_format($row['price'], 2); ?></span>
                            <span style="font-size:0.8rem; color: #888;">Stock: <?php echo $row['stock_quantity']; ?></span>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn">Add to Cart +</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>