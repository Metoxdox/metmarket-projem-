<?php
require 'db.php';

// strict authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Account Deletion
if (isset($_POST['delete_account'])) {
    // Delete user (Cart items will delete automatically due to CASCADE in SQL)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    session_destroy();
    header("Location: login.php");
    exit;
}

// Fetch User Info
$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>My Profile</title>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="brand">🍊 Grocery Market</a>
        <div class="nav-links">
            <a href="index.php">Shop</a>
            <a href="cart.php">My Cart 🛒</a>
        </div>
    </nav>

    <div class="form-container" style="max-width: 500px;">
        <h2 style="color:var(--primary);">My Profile</h2>
        <hr style="margin:15px 0; border: 0; border-top: 1px solid #eee;">
        
        <div style="margin-bottom: 20px;">
            <p style="margin-bottom:10px;"><strong>Username:</strong> <?php echo escape($user['username']); ?></p>
            <p style="margin-bottom:10px;"><strong>Email:</strong> <?php echo escape($user['email']); ?></p>
            <p style="margin-bottom:10px;"><strong>Member Since:</strong> <?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <form action="logout.php" method="POST" style="flex: 1;">
                <button type="submit" class="btn" style="background-color: #6c757d;">Log Out</button>
            </form>

            <form method="POST" onsubmit="return confirm('Are you sure? This is permanent!');" style="flex: 1;">
                <button type="submit" name="delete_account" class="btn btn-danger">Delete Account</button>
            </form>
        </div>
    </div>
</body>
</html>