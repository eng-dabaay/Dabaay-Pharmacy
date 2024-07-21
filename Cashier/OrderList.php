<?php
session_start();
include "./database/db.php";

$query = "SELECT * FROM orderlist";
$result = $conn->query($query);

$grand_total = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $grand_total += $row["total"];
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
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
                                        echo "<td>$" . number_format($row["total"], 2) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No orders saved yet.</td></tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="1"><strong>$<?php echo number_format($grand_total, 2); ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </main>
            <?php include "footer.php"; ?>
        </div>
    </div>
</body>
</html>
