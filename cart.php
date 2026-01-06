<?php
require 'db.php';

// Strict Auth Check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Remove Item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    header("Location: cart.php"); // Refresh to update totals
    exit;
}

// Handle "Checkout" (Clear Cart Simulation)
if (isset($_POST['checkout'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    echo "<script>alert('Order placed successfully! Thank you.'); window.location.href='index.php';</script>";
}

// Fetch Cart Items with Product Details
$query = "SELECT c.id as cart_id, c.quantity, p.name, p.price, p.image_url 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>My Cart</title>
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="brand">🍊 Grocery Market</a>
        <div class="nav-links">
            <a href="index.php">Continue Shopping</a>
            <a href="profile.php">Profile</a>
        </div>
    </nav>

    <div class="container">
        <h2>Your Shopping Cart</h2>
        <br>

        <?php if ($result->num_rows > 0): ?>
            <table style="width: 100%; box-shadow: var(--shadow);">
                <thead>
                    <tr>
                        <th style="width: 100px;">Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($item = $result->fetch_assoc()): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_price += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo escape($item['image_url']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td style="font-weight: 500;"><?php echo escape($item['name']); ?></td>
                        <td>₺<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td style="color: var(--primary); font-weight: bold;">₺<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="?remove=<?php echo $item['cart_id']; ?>" 
                               class="btn btn-danger" 
                               style="width: auto; padding: 5px 10px; font-size: 0.8rem; margin:0;">
                               Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div style="background: white; padding: 20px; margin-top: 20px; text-align: right; box-shadow: var(--shadow);">
                <h3>Total Amount: <span style="color: var(--primary);">₺<?php echo number_format($total_price, 2); ?></span></h3>
                <br>
                <form method="POST">
                    <button type="submit" name="checkout" class="btn" style="width: 200px; font-size: 1.1rem;">Confirm Order >></button>
                </form>
            </div>

        <?php else: ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                <h3 style="color: #666;">Your cart is currently empty.</h3>
                <br>
                <a href="index.php" class="btn" style="width: 200px;">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>