<?php
include "./database/db.php";

$info = "";
if (isset($_POST['medicine'])) {
    $name = $_POST['name'];
    $qty = $_POST['qty'];

    if (empty($name) || empty($qty)) {
        $info = "<font color='red'>Please fill all required fields</font>";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $info = "<font color='red'>Only letters and white space allowed in Medicine Name</font>";
    } elseif (!is_numeric($qty) || $qty <= 0) {
        $info = "<font color='red'>Quantity must be a positive number</font>";
    } else {
        $date = date("Y-m-d");

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $query = "UPDATE medicine SET name=?, qty=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $name, $qty, $id);
            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully'); window.location.href='Medicine.php';</script>";
            } else {
                echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            $query = "INSERT INTO medicine (name, qty) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $name, $qty);
            if ($stmt->execute()) {
                echo "<script>alert('Medicine added successfully'); window.location.href='Medicine.php';</script>";
            } else {
                echo "<script>alert('Error adding medicine: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        }
    }
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $query = "SELECT * FROM medicine WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $qty = $row['qty'];
    } else {
        echo "<script>alert('Medicine with ID $id not found');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
    <?php include "header.php" ?>
    <head>
        <style>
            .btn-wide {
                width: 100%;
                max-width: 300px;
            }
            .form-control-lg {
                height: calc(1.5em + 1rem + 2px);
                padding: 0.5rem 1rem;
                font-size: 1.25rem;
                line-height: 1.5;
                border-radius: 0.3rem;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <?php include "navbar.php" ?>
        <div id="layoutSidenav">
            <?php include "sidebar.php" ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4"><br>
                    <a href="Medicine.php" class="btn btn-primary">Review Table</a><br><br>
                    <div id="info"><?php if (isset($info)) echo $info; ?></div>
                        <form class="row g-3 wd-20" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Medicine Name</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter Medicine Name" required value="<?php if (isset($name)) echo $name; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="qty" class="form-label">Quantity</label>
                                <input type="text" class="form-control form-control-lg" id="qty" name="qty" placeholder="Enter Quantity" required value="<?php if (isset($qty)) echo $qty; ?>">
                            </div>
                            <div class="col-md-12 text-right">
                                <input type="submit" id="medicine" name="medicine" class="btn btn-primary btn-lg btn-fw" value="<?php echo isset($id) ? 'Update' : 'Save'; ?>">
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
