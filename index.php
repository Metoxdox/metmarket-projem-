<?php
require 'db.php';

if (isset($_POST['add_to_cart'])) {
 
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $p_id = intval($_POST['product_id']);
    $u_id = $_SESSION['user_id'];
    
    $check = $conn->query("SELECT * FROM cart WHERE user_id=$u_id AND product_id=$p_id");
    
    if($check->num_rows > 0){
      
        $conn->query("UPDATE cart SET quantity = quantity + 1 WHERE user_id=$u_id AND product_id=$p_id");
    } else {
        
        $conn->query("INSERT INTO cart (user_id, product_id) VALUES ($u_id, $p_id)");
    }
    
    
    echo "<script>alert('Urun Sepete Eklendi!'); window.location.href='index.php';</script>";
}


$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Met Market</title>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">🍎 Met Market</a>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Admin Panel</a>
                <?php else: ?>
                    <a href="profile.php">Profilim</a>
                    <a href="cart.php">Sepet 🛒</a>
                <?php endif; ?>
                
                <a href="logout.php" style="color:#dc3545;">Cikis</a>

            <?php else: ?>
                <a href="login.php">Giris</a>
                <a href="register.php">Kayit</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h2>Taze Taze </h2>
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
                            <span style="font-size:0.8rem; color: #888;">Stok: <?php echo $row['stock_quantity']; ?></span>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn">Sepete ekle +</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>
</html>
