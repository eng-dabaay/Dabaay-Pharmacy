<?php
include "./database/db.php";

function generatePassword() {
    return sprintf("%04d", rand(0, 9999));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $tel = $_POST['tel'];
    $type = $_POST['type'];
    $status = 'offline';
    $password = generatePassword();

    try {
        if (isset($_POST['id'])) {
            
            $id = $_POST['id'];
            $query = "UPDATE users SET username = ?, name = ?, tell = ?, password = ?, type = ?, status = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $username, $fullname, $tel, $password, $type, $status, $id);
        } else {
            
            $query = "INSERT INTO users (username, name, tell, password, type, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $username, $fullname, $tel, $password, $type, $status);
        }

        if ($stmt->execute()) {
            echo "User saved successfully!";
        } else {
            throw new Exception($stmt->error);
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { 
            echo "Error: Duplicate entry for phone number. Please use a different phone number.";
        } else {
            echo "Error: " . $e->getMessage();
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $stmt->close();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="User.php" class="btn btn-success">Review Table</a><br><br>
                    <form class="row g-3 wd-20" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <?php if (isset($user['id'])): ?>
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                        <?php endif; ?>
                        <div class="col-md-6">
                            <label for="fullname">Full Name:</label>
                            <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo isset($user['name']) ? htmlspecialchars($user['name']) : ''; ?>" required >
                        </div>
                        <div class="col-md-6">
                            <label for="tel">Phone No:</label>
                            <input type="text" class="form-control" id="tel" name="tel" value="<?php echo isset($user['tell']) ? htmlspecialchars($user['tell']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type">Type:</label>
                            <select id="type" name="type" class="form-control form-control-lg">
                                <option value="admin" <?php echo (isset($user['type']) && $user['type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="cashier" <?php echo (isset($user['type']) && $user['type'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                            </select>
                        </div>
                        <div class="col-md-12 text-right">
                            <button type="submit" name="save" class="btn btn-success btn-lg btn-fw">
                                <?php echo isset($user['id']) ? 'Update' : 'Save'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
