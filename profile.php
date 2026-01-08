<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


if (isset($_POST['delete_account'])) {

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    session_destroy();
    header("Location: login.php");
    exit;
}


$stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Profilim</title>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="brand">🍎 Met Market</a>
        <div class="nav-links">
            <a href="index.php">Market</a>
            <a href="cart.php">Sepetim 🛒</a>
        </div>
    </nav>

    <div class="form-container" style="max-width: 500px;">
        <h2 style="color:var(--primary);">Profilim</h2>
        <hr style="margin:15px 0; border: 0; border-top: 1px solid #eee;">
        
        <div style="margin-bottom: 20px;">
            <p style="margin-bottom:10px;"><strong>Kullanici Adi:</strong> <?php echo escape($user['username']); ?></p>
            <p style="margin-bottom:10px;"><strong>Email:</strong> <?php echo escape($user['email']); ?></p>
            <p style="margin-bottom:10px;"><strong>Uyelik Tarihi:</strong> <?php echo date("F j, Y", strtotime($user['created_at'])); ?></p>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <form action="logout.php" method="POST" style="flex: 1;">
                <button type="submit" class="btn" style="background-color: #6c757d;">Cikis Yap</button>
            </form>

            <form method="POST" onsubmit="return confirm('Emin Misiniz?');" style="flex: 1;">
                <button type="submit" name="delete_account" class="btn btn-danger">Hesabi Sil</button>
            </form>
        </div>
    </div>
</body>
</html>
