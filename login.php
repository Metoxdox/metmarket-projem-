<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';


if (isset($_GET['registered'])) {
    $success = "Hesabiniz basarili sekilde olusturuldu! Lutfen giris yapin.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

 
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
       
        if (password_verify($password, $user['password'])) {
         
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

         
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Yanlis Sifre.";
        }
    } else {
        $error = "Kullanici Adi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Giris - Met Market</title>
    
    <style>
       
        body {
            
            background-image: url('photos/giris.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh; 
            margin: 0;
        }
        
    </style>

</head>
<body>
    <div class="form-container">
        <h2 style="text-align:center; color: #85d33b; margin-bottom:20px;">Met Markete Hosgeldiniz</h2>
        
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
            <button type="submit" class="btn">Giris</button>
        </form>

        <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

        <div style="text-align:center;">
            <p>Bir hesabiniz yok mu?</p>
            <a href="register.php" class="btn" style="background-color: #6c757d; margin-top: 5px;">Yeni hesap olustur</a>
        </div>
    </div>
</body>
</html>
