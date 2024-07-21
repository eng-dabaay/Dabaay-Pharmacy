<?php

include "./database/db.php"; 

$id = $name = $tel = $age = $type_disease = $type_blood = $disease = $kg = $cm = $price = "";
$type_disease_positive = $quantity_positive = $positive_price = $subtotal_positive = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $tel = $_POST['tel'] ?? '';
    $age = $_POST['age'] ?? '';
    $type_disease = isset($_POST['type_disease']) ? implode(",", $_POST['type_disease']) : '';
    $type_blood = $_POST['type_blood'] ?? '';
    $disease = $_POST['disease'] ?? '';
    $kg = $_POST['kg'] ?? '';
    $cm = $_POST['cm'] ?? '';
    $price = $_POST['price'] ?? '';
    
    if ($disease == 'Positive') {
        $type_disease_positive = isset($_POST['type_disease_positive']) ? implode(",", $_POST['type_disease_positive']) : '';
        $quantity_positive = $_POST['quantity_positive'] ?? '';
        $positive_price = $_POST['positive_price'] ?? '';
        $subtotal_positive = $_POST['subtotal_positive'] ?? '';
    } else {
        $type_disease_positive = '';
        $quantity_positive = '';
        $positive_price = '';
        $subtotal_positive = '';
    }

    if (empty($id)) {
        $sql = "INSERT INTO customers (name, tel, age, type_disease, type_blood, disease, kg, cm, price, type_disease_positive, quantity_positive, positive_price, subtotal_positive) 
                VALUES ('$name', '$tel', '$age', '$type_disease', '$type_blood', '$disease', '$kg', '$cm', '$price', '$type_disease_positive', '$quantity_positive', '$positive_price', '$subtotal_positive')";
    } else {
        $sql = "UPDATE customers SET 
                    name='$name', tel='$tel', age='$age', type_disease='$type_disease', type_blood='$type_blood', disease='$disease', 
                    kg='$kg', cm='$cm', price='$price', 
                    type_disease_positive='$type_disease_positive', quantity_positive='$quantity_positive', positive_price='$positive_price', subtotal_positive='$subtotal_positive' 
                WHERE id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['order_message'] = "Record saved successfully!";
    } else {
        $_SESSION['order_message'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    header("Location: Customer.php");
    exit();
}

if (isset($_GET['id']) && !empty(trim($_GET['id']))) {
    $id = trim($_GET['id']);

    $sql = "SELECT * FROM customers WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        $param_id = $id;

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                $name = $row['name'];
                $tel = $row['tel'];
                $age = $row['age'];
                $type_disease = $row['type_disease'];
                $type_blood = $row['type_blood'];
                $disease = $row['disease'];
                $kg = $row['kg'];
                $cm = $row['cm'];
                $price = $row['price'];
                $type_disease_positive = $row['type_disease_positive'];
                $quantity_positive = $row['quantity_positive'];
                $positive_price = $row['positive_price'];
                $subtotal_positive = $row['subtotal_positive'];
            } else {
                header("Location: Customer.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        .hidden {
            display: none;
        }
    </style>
    <script>
        function togglePositiveFields() {
            var disease = document.getElementById("disease").value;
            var positiveFields = document.getElementById("positive-fields");
            
            if (disease === "Positive") {
                positiveFields.classList.remove("hidden");
            } else {
                positiveFields.classList.add("hidden");
                clearPositiveFields();
            }
        }

        function clearPositiveFields() {
            document.getElementById("type_disease_positive").selectedIndex = -1;
            document.getElementById("quantity_positive").value = '';
            document.getElementById("positive_price").value = '';
            document.getElementById("subtotal_positive").value = '';
        }

        function calculateSubtotal() {
            var quantity = parseFloat(document.getElementById("quantity_positive").value) || 0;
            var price = parseFloat(document.getElementById("positive_price").value) || 0;
            var subtotal = quantity * price;
            document.getElementById("subtotal_positive").value = subtotal.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            togglePositiveFields(); 
        });
    </script>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <a href="Customer.php" class="btn btn-success">Review Table</a><br><br>
                    <?php if (isset($_SESSION['order_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['order_message']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['order_message']); ?>
                    <?php endif; ?>
                    <form class="row g-3 wd-20" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        <div class="col-md-6">
                            <label for="name">Customer Name:</label>
                            <input type="text" id="name" name="name" class="form-control form-control-lg" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="tel">Telephone:</label>
                            <input type="text" id="tel" name="tel" class="form-control form-control-lg" value="<?php echo htmlspecialchars($tel); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="age">Age:</label>
                            <input type="text" id="age" name="age" class="form-control form-control-lg" value="<?php echo htmlspecialchars($age); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="type_disease">Type Disease:</label>
                            <select id="type_disease" name="type_disease[]" class="form-control form-control-lg" multiple required>
                                <option value="Malaria" <?php if (in_array('Malaria', explode(",", $type_disease))) echo 'selected'; ?>>Malaria</option>
                                <option value="Tiifow" <?php if (in_array('Tiifow', explode(",", $type_disease))) echo 'selected'; ?>>Tiifow</option>
                                <option value="Tiibisho" <?php if (in_array('Tiibisho', explode(",", $type_disease))) echo 'selected'; ?>>Tiibisho</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="type_blood">Type of Blood:</label>
                            <select id="type_blood" name="type_blood" class="form-control form-control-lg" required>
                                <option>Select Type Blood</option>
                                <option value="A+" <?php if ($type_blood == 'A+') echo 'selected'; ?>>A+</option>
                                <option value="A-" <?php if ($type_blood == 'A-') echo 'selected'; ?>>A-</option>
                                <option value="B+" <?php if ($type_blood == 'B+') echo 'selected'; ?>>B+</option>
                                <option value="B-" <?php if ($type_blood == 'B-') echo 'selected'; ?>>B-</option>
                                <option value="AB+" <?php if ($type_blood == 'AB+') echo 'selected'; ?>>AB+</option>
                                <option value="AB-" <?php if ($type_blood == 'AB-') echo 'selected'; ?>>AB-</option>
                                <option value="O+" <?php if ($type_blood == 'O+') echo 'selected'; ?>>O+</option>
                                <option value="O-" <?php if ($type_blood == 'O-') echo 'selected'; ?>>O-</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="disease">Disease:</label>
                            <select id="disease" name="disease" class="form-control form-control-lg" onchange="togglePositiveFields()" required>
                                <option>Type Disease</option>
                                <option value="Positive" <?php if ($disease == 'Positive') echo 'selected'; ?>>Positive</option>
                                <option value="Negative" <?php if ($disease == 'Negative') echo 'selected'; ?>>Negative</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kg">Weight (kg):</label>
                            <input type="text" id="kg" name="kg" class="form-control form-control-lg" value="<?php echo htmlspecialchars($kg); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cm">Height (cm):</label>
                            <input type="text" id="cm" name="cm" class="form-control form-control-lg" value="<?php echo htmlspecialchars($cm); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="price">Price:</label>
                            <input type="text" id="price" name="price" class="form-control form-control-lg" value="<?php echo htmlspecialchars($price); ?>" required>
                        </div>
                        <div id="positive-fields" class="<?php echo $disease == 'Positive' ? '' : 'hidden'; ?>">
                            <div class="col-md-6">
                                <label for="type_disease_positive">Type of Positive Disease:</label>
                                <select id="type_disease_positive" name="type_disease_positive[]" class="form-control form-control-lg" multiple>
                                    <option value="Malaria" <?php if (in_array('Malaria', explode(",", $type_disease_positive))) echo 'selected'; ?>>Malaria</option>
                                    <option value="Tiifow" <?php if (in_array('Tiifow', explode(",", $type_disease_positive))) echo 'selected'; ?>>Tiifow</option>
                                    <option value="Tiibisho" <?php if (in_array('Tiibisho', explode(",", $type_disease_positive))) echo 'selected'; ?>>Tiibisho</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="quantity_positive">Quantity:</label>
                                <input type="number" id="quantity_positive" name="quantity_positive" class="form-control form-control-lg" value="<?php echo htmlspecialchars($quantity_positive); ?>" oninput="calculateSubtotal()">
                            </div>
                            <div class="col-md-6">
                                <label for="positive_price">Positive Price:</label>
                                <input type="number" id="positive_price" name="positive_price" class="form-control form-control-lg" value="<?php echo htmlspecialchars($positive_price); ?>" oninput="calculateSubtotal()">
                            </div>
                            <div class="col-md-6">
                                <label for="subtotal_positive">Subtotal:</label>
                                <input type="number" id="subtotal_positive" name="subtotal_positive" class="form-control form-control-lg" value="<?php echo htmlspecialchars($subtotal_positive); ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <?php echo empty($id) ? 'Save' : 'Update'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
