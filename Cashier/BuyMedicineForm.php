<?php
include "./database/db.php";

$info = "";
if (isset($_POST['buy'])) {
    $name = $_POST['name'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];

    // Validate input
    if (empty($name) || empty($qty) || empty($price)) {
        $info = "<font color='red'>Please fill all required fields</font>";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $info = "<font color='red'>Only letters and white space allowed in Medicine Name</font>";
    } elseif (!is_numeric($qty) || $qty <= 0) {
        $info = "<font color='red'>Quantity must be a positive number</font>";
    } elseif (!is_numeric($price) || $price <= 0) {
        $info = "<font color='red'>Price must be a positive number</font>";
    } else {
        $date = date("Y-m-d");
        $subtotal = $qty * $price; // Correct subtotal calculation

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing record
            $id = $_POST['id'];
            $query = "UPDATE buymedicine SET name=?, qty=?, price=?, subtotal=? WHERE id=?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sidii", $name, $qty, $price, $subtotal, $id); // Adjusted bind_param types
            if ($stmt->execute()) {
                echo "<script>alert('Record updated successfully'); window.location.href='BuyMedicine.php';</script>";
            } else {
                echo "<script>alert('Error updating record: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            // Insert new record
            $insert_query = "INSERT INTO buymedicine (name, qty, price, subtotal, date) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sidss", $name, $qty, $price, $subtotal, $date); // Adjusted bind_param types
            if ($stmt->execute()) {
                echo "<script>alert('BuyMedicine added successfully'); window.location.href='BuyMedicine.php';</script>";
            } else {
                echo "<script>alert('Error adding buymedicine: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        }
    }
}

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $query = "SELECT * FROM buymedicine WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $qty = $row['qty'];
        $price = $row['price'];
    } else {
        echo "<script>alert('BuyMedicine with ID $id not found');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
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
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="BuyMedicine.php" class="btn btn-primary">Review Table</a><br><br>
                    <div id="info"><?php if (isset($info)) echo $info; ?></div>
                    <form class="row g-3 wd-20" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="id" value="<?php if (isset($id)) echo $id; ?>">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Medicine Name</label>
                            <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Enter Medicine Name" required value="<?php if (isset($name)) echo htmlspecialchars($name); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="qty" class="form-label">Quantity</label>
                            <input type="text" class="form-control form-control-lg" id="qty" name="qty" placeholder="Enter Quantity" required value="<?php if (isset($qty)) echo htmlspecialchars($qty); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price</label>
                            <input type="text" class="form-control form-control-lg" id="price" name="price" placeholder="Enter Price" required value="<?php if (isset($price)) echo htmlspecialchars($price); ?>">
                        </div>
                        <div class="col-md-12 text-right">
                            <input type="submit" id="buy" name="buy" class="btn btn-primary btn-lg btn-fw" value="<?php echo isset($id) ? 'Update' : 'Save'; ?>">
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
