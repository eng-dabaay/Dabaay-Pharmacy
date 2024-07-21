<?php
include "./database/db.php";

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM customers WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<p>Customer deleted successfully.</p>";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
}

$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE name LIKE ? OR tel LIKE ? OR type_disease LIKE ? OR disease LIKE ?");
    $likeSearchQuery = '%' . $searchQuery . '%';
    $stmt->bind_param("ssss", $likeSearchQuery, $likeSearchQuery, $likeSearchQuery, $likeSearchQuery);
} else {
    $stmt = $conn->prepare("SELECT * FROM customers");
}

$stmt->execute();
$result = $stmt->get_result();
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
                    <a href="CustomerForm.php" class="btn btn-primary">Add Customer</a><br><br>
                    <!-- Search Form -->
                    <form method="POST" action="Customer.php">
                        <div class="input-group mb-3">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, phone, disease..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>FullName</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th>Type Disease</th>
                                    <th>Blood</th>
                                    <th>Disease Type</th>
                                    <th>KG</th>
                                    <th>Height(cm)</th>
                                    <th>Price</th>
                                    <th>Disease(Positive)</th>
                                    <th>Quantity</th>
                                    <th>Positive Price</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row["id"] . "</td>";
                                        echo "<td>" . $row["name"] . "</td>";
                                        echo "<td>" . $row["tel"] . "</td>";
                                        echo "<td>" . $row["age"] . "</td>";
                                        echo "<td>" . $row["type_disease"] . "</td>";
                                        echo "<td>" . $row["type_blood"] . "</td>";
                                        echo "<td>" . $row["disease"] . "</td>";
                                        echo "<td>" . $row["kg"] . "</td>";
                                        echo "<td>" . $row["cm"] . "</td>";
                                        echo "<td>" . $row["price"] . "</td>";

                                        if ($row["disease"] == "Positive") {
                                            echo "<td>" . $row["type_disease_positive"] . "</td>";
                                            echo "<td>" . $row["quantity_positive"] . "</td>";
                                            echo "<td>" . $row["positive_price"] . "</td>";
                                            echo "<td>" . $row["subtotal_positive"] . "</td>";
                                        } else {
                                            echo "<td colspan='4'>Not applicable</td>";
                                        }
                                        echo "<td>" . $row["created_at"] . "</td>";
                                        echo "<td>";
                                        echo "<a href='CustomerForm.php?id=" . $row["id"] . "' class='btn btn-primary'><i class='fa fa-pencil'></i></a> &nbsp;&nbsp;";
                                        echo "<a href='?action=delete&id=" . $row["id"] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this record?\")'><i class='fa fa-trash'></i></a>&nbsp;&nbsp;";
                                        echo "<a href='print.php?id=" . $row["id"] . "' class='btn btn-success'><i class='fa fa-print'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='15'>No records found</td></tr>";
                                }
                                $stmt->close();
                                $conn->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
