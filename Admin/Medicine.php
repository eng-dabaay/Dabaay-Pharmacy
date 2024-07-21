<?php
include "./database/db.php";

$search = "";
$result = null;
$alert_message = "";

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $query = "SELECT * FROM medicine WHERE name LIKE ?";
    $stmt = $conn->prepare($query);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
} else {
    $query = "SELECT * FROM medicine";
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET["deleted_id"])) {
    $deleted_id = $_GET["deleted_id"];
    $stmt_del = $conn->prepare("DELETE FROM medicine WHERE id = ?");
    $stmt_del->bind_param("i", $deleted_id);
    if ($stmt_del->execute()) {
        $alert_message = "<div class='alert alert-success alert-dismissible fade show' role='alert'>Medicine has been deleted successfully<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
    } else {
        $alert_message = "<div class='alert alert-danger alert-dismissible fade show' role='alert'>Error deleting medicine<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
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
                    <a href="MedicineForm.php" class="btn btn-primary">Add Medicine</a><br><br>
                    <form method="get" action="Medicine.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by medicine name" value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    <?php if ($alert_message): ?>
                        <?php echo $alert_message; ?>
                    <?php endif; ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["qty"]) . "</td>";
                                        echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                                        echo "<td>
                                        <a href='MedicineForm.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-primary' onclick='return confirmUpdate()'><i class='fa fa-pencil'></i></a>
                                        <a href='Medicine.php?deleted_id=" . htmlspecialchars($row["id"]) . "' class='btn btn-danger' onclick='return confirmDelete()'><i class='fa fa-trash'></i></a>
                                        </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No records found</td></tr>";
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
            return confirm('Do you want to delete this Medicine?');
        }

        function confirmUpdate() {
            return confirm('Do you want to update this Medicine?');
        }
    </script>
</body>
</html>
