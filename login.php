<?php
session_start();
include "./Admin/database/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $type = $_POST['type'];
    $remember = isset($_POST['remember']);

    if (empty($email) || empty($password) || empty($type)) {
        echo "All fields are required!";
        exit;
    }

    $query = "SELECT * FROM users WHERE username = ? AND password = ? AND type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $email, $password, $type);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;

        $updateQuery = "UPDATE users SET last_activity = NOW(), status = 'online' WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param('s', $email);
        $updateStmt->execute();
        $updateStmt->close();

        if ($remember) {
            setcookie('email', $email, time() + (30 * 24 * 60 * 60), '/');
            setcookie('password', $password, time() + (30 * 24 * 60 * 60), '/');
            setcookie('type', $type, time() + (30 * 24 * 60 * 60), '/');
        } else {
            setcookie('email', '', time() - 3600, '/');
            setcookie('password', '', time() - 3600, '/');
            setcookie('type', '', time() - 3600, '/');
        }

        $stmt->close();
        $conn->close();

        if ($user['type'] === 'admin') {
            header("Location: ./Admin/index.php");
        } else if ($user['type'] === 'cashier') {
            header("Location: ./Cashier/index.php");
        }
        exit;
    } else {
        echo "Invalid username, password, or user type.";
    }

    $stmt->close();
    $conn->close();
} else {
    // echo "Invalid request method.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Login - SB Admin</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
        .btn-lg-full {
            width: 100%;
        }
    </style>
</head>
<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                                <div class="card-body">
                                    <form action="login.php" method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" name="email" type="text" placeholder="name@example.com" value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" required />
                                            <label for="inputEmail">Email address</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" value="<?php echo isset($_COOKIE['password']) ? htmlspecialchars($_COOKIE['password']) : ''; ?>" required />
                                            <label for="inputPassword">Password</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="inputType" name="type" aria-label="Select Type" required>
                                                <option value="" disabled>Select Type</option>
                                                <option value="admin" <?php echo isset($_COOKIE['type']) && $_COOKIE['type'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="cashier" <?php echo isset($_COOKIE['type']) && $_COOKIE['type'] === 'cashier' ? 'selected' : ''; ?>>Cashier</option>
                                            </select>
                                            <label for="inputType">User Type</label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" id="inputRememberPassword" type="checkbox" name="remember" value="1" <?php echo isset($_COOKIE['email']) ? 'checked' : ''; ?> />
                                            <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Dabaay Pharmacy 2024</div>
                        <div>
                            <a href="./Privacy Policy.php">Privacy Policy</a>
                            &middot;
                            <a href="./Terms & Conditions.php">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
