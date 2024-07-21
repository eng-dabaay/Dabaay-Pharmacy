<?php
include "./database/db.php";

$search = "";
$result = null;

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM buymedicine WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $query = "SELECT * FROM buymedicine";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET["deleted_id"])) {
    $deleted_id = $_GET["deleted_id"];
    $stmt_del = $conn->prepare("DELETE FROM buymedicine WHERE id = ?");
    $stmt_del->bind_param("i", $deleted_id);
    if ($stmt_del->execute()) {
        echo "<script>alert('BuyMedicine has been deleted successfully'); window.location.href='BuyMedicine.php';</script>";
    } else {
        echo "<script>alert('Error deleting BuyMedicine');</script>";
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
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="BuyMedicineForm.php" class="btn btn-primary">Add Medicine</a><br><br>
                    <form method="get" action="BuyMedicine.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by medicine name" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th> 
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $subtotal = $row["qty"] * $row["price"]; 
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["qty"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["price"]) . "</td>";
                                        echo "<td>" . number_format($subtotal, 2) . "</td>"; 
                                        echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                                        echo "<td>
                                        <a href='BuyMedicineForm.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-primary' onclick='return confirmUpdate()'><i class='fa fa-pencil'></i></a>
                                        <a href='BuyMedicine.php?deleted_id=" . htmlspecialchars($row["id"]) . "' class='btn btn-danger' onclick='return confirmDelete()'><i class='fa fa-trash'></i></a>
                                        <button class='btn btn-info' onclick='printPDF(" . htmlspecialchars($row["id"]) . ")'><i class='fa fa-print'></i></button>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No records found</td></tr>"; 
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        function confirmDelete() {
            return confirm('Do you want to delete this BuyMedicine?');
        }

        function confirmUpdate() {
            return confirm('Do you want to update this BuyMedicine?');
        }

        function printPDF(id) {
            window.open('generatePDF.php?id=' + id, '_blank');
        }
    </script>
</body>
</html>
