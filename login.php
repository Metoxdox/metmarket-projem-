<?php
require 'db.php';

// If user is already logged in, redirect them
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

// Check if user just registered
if (isset($_GET['registered'])) {
    $success = "Account created successfully! Please login.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Secure Prepared Statement
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verify Hashed Password
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Role Based Redirect
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Giris - Met Market</title>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align:center; color: 	#85d33b; margin-bottom:20px;">Met Markete Hosgeldiniz</h2>
        
        <?php if($success): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

        <div style="text-align:center;">
            <p>Bir hesabiniz yok mu?</p>
            <a href="register.php" class="btn" style="background-color: #6c757d; margin-top: 5px;">Yeni hesap olustur</a>
        </div>
    </div>
</body>
</html>