<?php
session_start();
include "./database/db.php"; 

$medicines = [];
$product_name = $quantity = $price = $total = $image = '';
$update_id = $medicine_id = null; 

$stmt = $conn->prepare("SELECT id, name, qty FROM medicine ORDER BY name");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $medicines[] = $row;
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $update_id = $_GET['id'];
    $stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt_order->bind_param("i", $update_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    if ($result_order->num_rows > 0) {
        $order = $result_order->fetch_assoc();
        $medicine_id = $order['medicine_id'];
        $quantity = $order['quantity'];
        $price = $order['price'];
        $total = $order['total'];
        $image = $order['image'];

        $stmt_medicine = $conn->prepare("SELECT name FROM medicine WHERE id = ?");
        $stmt_medicine->bind_param("i", $medicine_id);
        $stmt_medicine->execute();
        $stmt_medicine->bind_result($product_name);
        $stmt_medicine->fetch();
        $stmt_medicine->close();
    }
    $stmt_order->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save']) || isset($_POST['update'])) {
        $update_id = $_POST['update_id'];
        $medicine_id = $_POST["medicine_id"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
        $total = $quantity * $price;

        $stmt_medicine = $conn->prepare("SELECT name FROM medicine WHERE id = ?");
        $stmt_medicine->bind_param("i", $medicine_id);
        $stmt_medicine->execute();
        $stmt_medicine->bind_result($product_name);
        $stmt_medicine->fetch();
        $stmt_medicine->close();

        $fields = [];
        $params = [];
        $types = '';

        if ($_FILES['image']['size'] > 0) {
            $image = $_FILES['image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
            $fields[] = "image = ?";
            $params[] = $image;
            $types .= 's';
        }

        if (isset($update_id) && !empty($update_id)) {
            if ($medicine_id != $order['medicine_id']) {
                $fields[] = "medicine_id = ?";
                $params[] = $medicine_id;
                $types .= 'i';
            }
            if ($product_name != $order['product_name']) {
                $fields[] = "product_name = ?";
                $params[] = $product_name;
                $types .= 's';
            }
            if ($quantity != $order['quantity']) {
                $fields[] = "quantity = ?";
                $params[] = $quantity;
                $types .= 'i';
            }
            if ($price != $order['price']) {
                $fields[] = "price = ?";
                $params[] = $price;
                $types .= 'd';
            }
            if ($total != $order['total']) {
                $fields[] = "total = ?";
                $params[] = $total;
                $types .= 'd';
            }

            if (count($fields) > 0) {
                $sql = "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = ?";
                $params[] = $update_id;
                $types .= 'i';
                $stmt_update = $conn->prepare($sql);
                $stmt_update->bind_param($types, ...$params);
                if ($stmt_update->execute()) {
                    $_SESSION['order_message'] = "Order updated successfully!";
                } else {
                    echo "<div class='alert alert-danger'>Error updating order.</div>";
                }
                $stmt_update->close();
            }
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO orders (medicine_id, product_name, quantity, price, total, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("isisis", $medicine_id, $product_name, $quantity, $price, $total, $image);
            if ($stmt_insert->execute()) {
                $_SESSION['order_message'] = "Order saved successfully!";
            } else {
                echo "<div class='alert alert-danger'>Error saving order.</div>";
            }
            $stmt_insert->close();
        }

        header("Location: Order.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <script>
        function updateTotal() {
            var quantity = parseFloat(document.getElementById("quantity").value);
            var price = parseFloat(document.getElementById("price").value);
            var total = quantity * price;
            document.getElementById("total").value = total.toFixed(2);
        }
    </script>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="Order.php" class="btn btn-success">Review Table</a><br><br>
                    <?php if (isset($_SESSION['order_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['order_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['order_message']); ?>
                    <?php endif; ?>
                    <form class="row g-3 wd-20" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="update_id" value="<?php echo isset($update_id) ? $update_id : ''; ?>">
                        <div class="col-md-6">
                            <label for="medicine_id">Product Name:</label>
                            <select class="form-control" id="medicine_id" name="medicine_id" required>
                                <option value="">Select a medicine</option>
                                <?php foreach ($medicines as $medicine): ?>
                                    <option value="<?php echo htmlspecialchars($medicine['id']); ?>" <?php echo ($medicine_id == $medicine['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($medicine['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="quantity">Quantity:</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required onchange="updateTotal()">
                        </div>
                        <div class="col-md-6">
                            <label for="price">Price:</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required onchange="updateTotal()">
                        </div>
                        <div class="col-md-6">
                            <label for="total">Total:</label>
                            <input type="number" step="0.01" class="form-control" id="total" name="total" value="<?php echo htmlspecialchars($total); ?>" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="image">Upload Image:</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            <?php if (!empty($image)): ?>
                                <img src="uploads/<?php echo htmlspecialchars($image); ?>" class="mt-2 mb-2" style="max-width: 200px; max-height: 200px;" alt="Product Image">
                            <?php endif; ?>
                        </div>
                        <?php if (!isset($update_id)): ?>
                            <div class="col-md-12 text-right">
                                <button type="submit" name="save" class="btn btn-success btn-lg btn-fw">Save</button>
                            </div>
                        <?php else: ?>
                            <div class="col-md-12 text-right">
                                <button type="submit" name="update" class="btn btn-success btn-lg btn-fw">Update</button>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
