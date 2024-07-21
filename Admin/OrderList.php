<?php
session_start();
include "./database/db.php";

if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $deleteQuery = "DELETE FROM orderlist WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: OrderList.php");
    exit();
}

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT id, product_name, price, quantity, (price * quantity) AS subtotal, image FROM orderlist WHERE product_name LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%" . $search . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $query = "SELECT id, product_name, price, quantity, (price * quantity) AS subtotal, image FROM orderlist";
    $result = $conn->query($query);
}

$grand_total = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grand_total += $row["subtotal"];
    }
    $result->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        .table-container {
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
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
                    <h2>Saved Orders</h2>
                    <form method="get" action="OrderList.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by product name" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["product_name"]) . "</td>";
                                        echo '<td><img src="uploads/' . htmlspecialchars($row["image"]) . '" alt="Product Image" style="max-height: 100px; object-fit: cover;"></td>';
                                        echo "<td>$" . number_format($row["price"], 2) . "</td>";
                                        echo "<td>" . $row["quantity"] . "</td>";
                                        echo "<td>$" . number_format($row["subtotal"], 2) . "</td>";
                                        echo "<td>";
                                        echo "<a href='OrderList.php?delete=" . htmlspecialchars($row["id"]) . "' class='btn btn-danger'>Delete</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No orders saved yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="1"><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
