<?php
session_start();
include "./database/db.php"; 


$search_query = "";
$search_param = "";

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $search_param = "%" . $search_query . "%";
}


$orders = [];
if (!empty($search_query)) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE product_name LIKE ?");
    $stmt->bind_param("s", $search_param);
} else {
    $stmt = $conn->prepare("SELECT * FROM orders");
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['deleted_id'])) {
    $delete_id = $_GET['deleted_id'];
    $stmt_delete = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt_delete->bind_param("i", $delete_id);
    if ($stmt_delete->execute()) {
        $_SESSION['delete_message'] = "Order deleted successfully!";
        header("Location: Order.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting order.</div>";
    }
    $stmt_delete->close();
}

$update_id = $product_name = $quantity = $price = $total = $image = '';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $update_id = $_GET['id'];
    $stmt_order = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt_order->bind_param("i", $update_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    if ($result_order->num_rows > 0) {
        $order = $result_order->fetch_assoc();
        $product_name = $order['product_name'];
        $quantity = $order['quantity'];
        $price = $order['price'];
        $total = $order['total'];
        $image = $order['image'];
    }
    $stmt_order->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $update_id = $_POST['update_id'];
        $product_name = $_POST["product_name"];
        $quantity = $_POST["quantity"];
        $price = $_POST["price"];
        $total = $quantity * $price;

        if ($_FILES['image']['size'] > 0) {
            
            $image = $_FILES['image']['name'];
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        if (!empty($update_id)) {
            
            $stmt_update = $conn->prepare("UPDATE orders SET product_name = ?, quantity = ?, price = ?, total = ?, image = ? WHERE id = ?");
            $stmt_update->bind_param("siisii", $product_name, $quantity, $price, $total, $image, $update_id);
            $action = "updated";
        } else {
           
            $stmt_insert = $conn->prepare("INSERT INTO orders (product_name, quantity, price, total, image) VALUES (?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("siiss", $product_name, $quantity, $price, $total, $image);
            $action = "added";
        }

        if (!empty($update_id)) {
            if ($stmt_update->execute()) {
                $_SESSION['order_message'] = "Order updated successfully!";
            } else {
                echo "<div class='alert alert-danger'>Error updating order.</div>";
            }
            $stmt_update->close();
        } else {
            if ($stmt_insert->execute()) {
                $_SESSION['order_message'] = "Order added successfully!";
            } else {
                echo "<div class='alert alert-danger'>Error adding order.</div>";
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
    <style>
        .table thead th {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="OrderForm.php" class="btn btn-primary">Add Order</a><br><br>
                    <?php if (isset($_SESSION['order_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['order_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['order_message']); ?>
                    <?php endif; ?>

                    <!-- Search Form -->
                    <form method="get" action="Order.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by product name" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Image</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($orders)): ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order["id"]); ?></td>
                                            <td><?php echo htmlspecialchars($order["product_name"]); ?></td>
                                            <td><?php echo htmlspecialchars($order["quantity"]); ?></td>
                                            <td>$<?php echo number_format($order["price"], 2); ?></td>
                                            <td>$<?php echo number_format($order["total"], 2); ?></td>
                                            <td>
                                                <?php if (!empty($order["image"])): ?>
                                                    <img src="uploads/<?php echo htmlspecialchars($order["image"]); ?>" class="img-fluid" style="max-width: 100px; max-height: 100px;" alt="Product Image">
                                                <?php else: ?>
                                                    No Image
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($order["created_at"]); ?></td>
                                            <td>
                                                <a href="OrderForm.php?id=<?php echo htmlspecialchars($order["id"]); ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                <a href="Order.php?deleted_id=<?php echo htmlspecialchars($order["id"]); ?>" class="btn btn-danger" onclick="return confirm('Do you want to delete this Order?')"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8">No orders found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
