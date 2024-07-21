<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<body class="sb-nav-fixed">
    <?php include "header.php"; ?>
    <?php include "navbar.php"; ?>
    <?php include "sidebar.php"; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <section class="vh-50" style="background-color: #f4f5f7;">
                    <div class="container py-5 h-100">
                        <div class="row d-flex justify-content-center align-items-center h-100">
                            <div class="col col-lg-6 mb-4 mb-lg-0">
                                <br><br>
                                <div class="card mb-3" style="border-radius: .5rem;">
                                    <div class="row g-0">
                                        <div class="col-md-4 gradient-custom text-center text-white"
                                             style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                            <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                                            <p><?php echo htmlspecialchars($user['type']); ?></p>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body p-4">
                                                <h6>Profile</h6>
                                                <hr class="mt-0 mb-4">
                                                <div class="row pt-1">
                                                    <div class="col-6 mb-3">
                                                        <h6>Fullname</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['name']); ?></p>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <h6>Phone</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['tell']); ?></p>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <h6>Username</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['username']); ?></p>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <h6>Password</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['password']); ?></p>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <h6>User Type</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['type']); ?></p>
                                                    </div>
                                                    <div class="col-6 mb-3">
                                                        <h6>Status</h6>
                                                        <p class="text-muted"><?php echo htmlspecialchars($user['status']); ?></p>
                                                    </div>
                                                </div>
                                                <a href="edit_profile.php" class="btn btn-danger">Edit Profile</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
