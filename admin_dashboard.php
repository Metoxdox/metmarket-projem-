<?php
require 'db.php';

// Access Control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle Add Product
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock_quantity) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $name, $desc, $price, $stock);
    $stmt->execute();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: admin_dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="brand">Admin Panel</a>
        <div class="nav-links">
            <a href="index.php">View Site</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h3>Add New Product</h3>
        <form method="POST" style="background:white; padding:20px; margin-bottom:30px; border:1px solid #ddd;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <input type="text" name="name" placeholder="Product Name" required style="padding:10px;">
                <input type="number" step="0.01" name="price" placeholder="Price" required style="padding:10px;">
                <input type="number" name="stock" placeholder="Stock Quantity" required style="padding:10px;">
                <input type="text" name="description" placeholder="Description" required style="padding:10px;">
            </div>
            <button type="submit" name="add_product" class="btn" style="width:auto; margin-top:15px;">Add Product</button>
        </form>

        <h3>Inventory Management</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $conn->query("SELECT * FROM products ORDER BY id DESC");
                while($p = $products->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo escape($p['name']); ?></td>
                    <td>₺<?php echo number_format($p['price'], 2); ?></td>
                    <td><?php echo $p['stock_quantity']; ?></td>
                    <td>
                        <a href="?delete=<?php echo $p['id']; ?>" class="btn btn-danger" style="padding:5px 10px; font-size:0.8rem;" onclick="return confirm('Delete this item?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>