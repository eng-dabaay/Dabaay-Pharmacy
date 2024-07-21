<?php
include "./database/db.php";

$search = "";
$result = null;

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM orders WHERE product_name LIKE ?";
    $stmt = $conn->prepare($query);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $query = "SELECT * FROM orders";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET["deleted_id"])) {
    $deleted_id = $_GET["deleted_id"];
    $stmt_del = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt_del->bind_param("i", $deleted_id);
    if ($stmt_del->execute()) {
        echo "<script>alert('Order has been deleted successfully'); window.location.href='Orders.php';</script>";
    } else {
        echo "<script>alert('Error deleting order');</script>";
    }
    $stmt_del->close();
}

$stmt->close();
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
        .card-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card {
            flex: 1;
            max-width: 300px;
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
                    <form method="get" action="Orders.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by orders name" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="card-group">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<div class='card'>";
                                if ($row["image"]) {
                                    echo '<img src="uploads/' . htmlspecialchars($row["image"]) . '" class="card-img-top" alt="Product Image" style="max-height: 200px; object-fit: cover;">';
                                } else {
                                    echo '<img src="path/to/default-image.jpg" class="card-img-top" alt="No Image Available" style="max-height: 200px; object-fit: cover;">';
                                }
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . htmlspecialchars($row["product_name"]) . "</h5>";
                                echo "<p class='card-text'>Price: $" . number_format($row["price"], 2) . "</p>";
                                echo "<p class='card-text'>Quantity: " . htmlspecialchars($row["quantity"]) . "</p>";
                                echo "<a href='cart.php?add=" . htmlspecialchars($row["id"]) . "' class='btn btn-primary'>Add to Cart</a>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p>No orders found</p>";
                        }
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
