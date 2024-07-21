<?php
include "./database/db.php";

$search_query = isset($_GET['search']) ? $_GET['search'] : '';

if (!empty($search_query)) {
    $query = "SELECT id, username, name, tell, password, type, status, date, last_activity FROM users WHERE type LIKE ?";
    $stmt = $conn->prepare($query);
    $search_param = "%$search_query%";
    $stmt->bind_param('s', $search_param);
} else {
    $query = "SELECT id, username, name, tell, password, type, status, date, last_activity FROM users";
    $stmt = $conn->prepare($query);
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
                    <a href="UserForms.php" class="btn btn-primary">Add Users</a><br><br>

                    <!-- Search Form -->
                    <form method="get" action="Users.php">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Search by type" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                              <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Phone No</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Last Activity</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                  <td><?php echo htmlspecialchars($row['id']); ?></td>
                                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                                  <td><?php echo htmlspecialchars($row['tell']); ?></td>
                                  <td><?php echo htmlspecialchars($row['username']); ?></td>
                                  <td><?php echo htmlspecialchars($row['password']); ?></td>
                                  <td><?php echo htmlspecialchars($row['type']); ?></td>
                                  <td><?php echo htmlspecialchars($row['status']); ?></td>
                                  <td><?php echo htmlspecialchars($row['date']); ?></td>
                                  <td><?php echo htmlspecialchars($row['last_activity']); ?></td>
                                  <td>
                                    <a href="UserForms.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
                                    <a href="deleteUser.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash"></i></a>
                                  </td>
                                </tr>
                              <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
