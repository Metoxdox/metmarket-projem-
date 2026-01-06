<?php
require 'db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role']; // captured from the form

    // Basic Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "This email is already registered.";
        } else {
            // Hash the password (Security Best Practice)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into Database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
            
            if ($stmt->execute()) {
                // Redirect to login page with a success signal
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Kayit - Met Market</title>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align:center; color:var(--primary); margin-bottom:20px;">Hesap Olustur</h2>
        
        <?php if($error): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Kullanici Adi</label>
                <input type="text" name="username" required>
            </div>
            
            <div class="form-group">
                <label>Email Adresi</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Sifre</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Account Type (For Testing)</label>
                <select name="role" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="user">Regular User (Customer)</option>
                    <option value="admin">Admin (Manager)</option>
                </select>
            </div>

            <button type="submit" class="btn">Kayit Ol</button>
        </form>

        <p style="text-align:center; margin-top:20px;">
          Daha onceden hesabiniz var mi? <br>
            <a href="login.php" style="color:var(--primary); font-weight:bold;">Kayit Ol</a>
        </p>
    </div>
</body>
</html>