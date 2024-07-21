<?php
include "./database/db.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    echo "No customer ID specified.";
    exit();
}

$pharmacyName = "Dabaay Pharmacy";
$pharmacyNumber = "+252 617 083069";

$currentDate = date("Y-m-d");

$subtotal = 0;

if ($customer['disease'] == "Positive") {
    $quantity = $customer['quantity_positive'];
    $positivePrice = $customer['positive_price'];
    $subtotal = $customer['price'] + ($quantity * $positivePrice);
}
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
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        .print-button {
            margin-bottom: 20px;
        }
        .customer-details {
            border-collapse: collapse;
            width: 100%;
        }
        .customer-details th, .customer-details td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .customer-details th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        .header p {
            margin: 0;
        }
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
     <script>
        function printPage() {
            window.print();
        }
    </script>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4"><br>
                    <div class="container">
                        <div class="header">
                            <h2><?php echo $pharmacyName; ?></h2>
                            <p>Contact: <?php echo $pharmacyNumber; ?></p>
                            <p>Date: <?php echo $currentDate; ?></p>
                        </div>
                        <button class="print-button btn btn-danger" onclick="printPage()">Print</button>
                        <h2>Description Letter</h2>
                        <table class="customer-details">
                            <tr>
                                <th>ID</th>
                                <td><?php echo htmlspecialchars($customer['id']); ?></td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td><?php echo htmlspecialchars($customer['tel']); ?></td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td><?php echo htmlspecialchars($customer['age']); ?></td>
                            </tr>
                            <tr>
                                <th>Type of Disease</th>
                                <td><?php echo htmlspecialchars($customer['type_disease']); ?></td>
                            </tr>
                            <tr>
                                <th>Blood</th>
                                <td><?php echo htmlspecialchars($customer['type_blood']); ?></td>
                            </tr>
                            <tr>
                                <th>Disease Type</th>
                                <td><?php echo htmlspecialchars($customer['disease']); ?></td>
                            </tr>
                            <tr>
                                <th>Weight (kg)</th>
                                <td><?php echo htmlspecialchars($customer['kg']); ?></td>
                            </tr>
                            <tr>
                                <th>Height (cm)</th>
                                <td><?php echo htmlspecialchars($customer['cm']); ?></td>
                            </tr>
                            <tr>
                                <th>Price</th>
                                <td><?php echo htmlspecialchars($customer['price']); ?></td>
                            </tr>
                            <?php if ($customer['disease'] == "Positive"): ?>
                            <tr>
                                <th>Disease (Positive)</th>
                                <td><?php echo htmlspecialchars($customer['type_disease_positive']); ?></td>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <td><?php echo htmlspecialchars($customer['quantity_positive']); ?></td>
                            </tr>
                            <tr>
                                <th>Positive Price</th>
                                <td><?php echo htmlspecialchars($customer['positive_price']); ?></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <td><?php echo htmlspecialchars($customer['subtotal_positive']); ?></td>
                            </tr>
                            <tr>
                                <th>Subtotal</th>
                                <td><?php echo number_format($subtotal, 2); ?></td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <th>Subtotal</th>
                                <td><?php echo number_format($customer['price'], 2); ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <th>Date</th>
                                <td><?php echo htmlspecialchars($customer['created_at']); ?></td>
                            </tr>
                        </table><br><br>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
