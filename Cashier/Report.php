<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: black;
            color: white; 
        }
        button {
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
        }
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 60px;
            margin-right: 20px;
        }
        .header .details {
            line-height: 1.5;
        }
        .grand-total {
            margin-top: 20px;
            font-weight: bold;
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
                <h2>Orders Report</h2><br><br>
                    <!-- Search Form -->
                    <form method="POST" action="Report.php">
                        <div class="input-group mb-3">
                            <label for="name">Start Date:</label>
                            <input type="date" id="start_date" class="form-control" name="start_date">
                        </div>
                        <div class="input-group mb-3">
                            <label for="name">End Date:</label>
                            <input type="date" id="end_date" class="form-control" name="end_date">
                        </div>
                        <button type="submit" class="btn btn-success" name="filter">Filter</button>
                    </form><br><br>
                    <div class="table-responsive">
                        <table class="table table-hover text-center">
                            <?php
                            $host = 'localhost'; 
                            $db = 'pharmacy';
                            $user = 'root';
                            $pass = '';
                            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
                            $options = [
                                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                PDO::ATTR_EMULATE_PREPARES   => false,
                            ];
                            try {
                                $pdo = new PDO($dsn, $user, $pass, $options);
                            } catch (\PDOException $e) {
                                throw new \PDOException($e->getMessage(), (int)$e->getCode());
                            }
                            if (isset($_POST['filter'])) {
                                $start_date = $_POST['start_date'];
                                $end_date = $_POST['end_date'];

                                $sql = "SELECT id, product_name, price, quantity, total, date FROM orderlist WHERE date BETWEEN :start_date AND :end_date";
                                $stmt = $pdo->prepare($sql);
                                $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
                                $orders = $stmt->fetchAll();

                                $grand_total = 0;
                                if ($orders) {
                                    echo "<table id='orderTable'>
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Product Name</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                    <th>Date</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                    foreach ($orders as $order) {
                                        $order_id = htmlspecialchars($order['id']);
                                        $product_name = htmlspecialchars($order['product_name']);
                                        $price = htmlspecialchars($order['price']);
                                        $quantity = htmlspecialchars($order['quantity']);
                                        $total = htmlspecialchars($order['total']);
                                        $date = htmlspecialchars($order['date']);
                                        $grand_total += $total;
                                        echo "<tr>
                                                <td>$order_id</td>
                                                <td>$product_name</td>
                                                <td>$price</td>
                                                <td>$quantity</td>
                                                <td>$total</td>
                                                <td>$date</td>
                                                <td><button onclick=\"printRow($order_id)\" class='btn btn-success'><i class='fa fa-print'></i></button></td>
                                            </tr>";
                                    }
                                    echo "<tr class='grand-total'>
                                            <td colspan='4'>Grand Total:</td>
                                            <td colspan='3'>$" . number_format($grand_total, 2) . "</td>
                                        </tr>";
                                    echo "</tbody></table>";
                                } else {
                                    echo "No records found.";
                                }
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        function printRow(orderId) {
            var rows = document.querySelectorAll('#orderTable tr');
            var printContent = '';
            var headerContent = '';
            var grandTotal = document.querySelector('.grand-total').textContent;
            rows.forEach(function(row, index) {
                if (row.cells[0] && row.cells[0].textContent == orderId) {
                    var cells = row.children;
                    var printRowContent = '';
                    for (var i = 0; i < cells.length - 1; i++) {
                        printRowContent += cells[i].outerHTML;
                    }
                    printContent = printRowContent;
                }
                if (index === 0) { 
                    var headerCells = row.children;
                    var printHeaderContent = '';
                    for (var j = 0; j < headerCells.length - 1; j++) {
                        printHeaderContent += headerCells[j].outerHTML;
                    }
                    headerContent = printHeaderContent;
                }
            });

            if (printContent) {
                var printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write('<html><head><title>Print Order</title>');
                printWindow.document.write('<style>');
                printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
                printWindow.document.write('th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }');
                printWindow.document.write('th { background-color: black; color: white; }'); 
                printWindow.document.write('.header { display: flex; align-items: center; margin-bottom: 20px; }');
                printWindow.document.write('.header img { height: 60px; margin-right: 20px; }');
                printWindow.document.write('.header .details { line-height: 1.5; }');
                printWindow.document.write('.grand-total { margin-top: 20px; font-weight: bold; }');
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body >');
                printWindow.document.write('<div class="header">');
                printWindow.document.write('<img src="path/to/your/logo.png" alt="Pharmacy Logo">'); 
                printWindow.document.write('<div class="details">');
                printWindow.document.write('<h2>Dabaay Pharmacy</h2>'); 
                printWindow.document.write('<p>Telephone: (+252) 617083069</p>'); 
                printWindow.document.write('<p>Date: ' + new Date().toLocaleDateString() + '</p>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');
                printWindow.document.write('<table border="1" cellpadding="5" cellspacing="0">');
                printWindow.document.write('<thead><tr>' + headerContent + '</tr></thead>');
                printWindow.document.write('<tbody><tr>' + printContent + '</tr></tbody>');
                printWindow.document.write('</table>');
                printWindow.document.write('<div class="grand-total">Grand Total: ' + grandTotal + '</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
            }
        }
    </script>
</body>
</html>
