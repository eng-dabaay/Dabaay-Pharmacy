<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        .print-button {
            cursor: pointer;
            color: blue;
            text-decoration: underline;
        }

        @media print {
            .print-button {
                display: none; 
            }
            .no-print {
                display: none; 
            }
            .header-print {
                display: block;
            }
            @page {
                size: auto; 
                margin: 0; 
            }
            .print-header {
                display: none; 
            }
            .print-footer {
                display: none; 
            }
        }

        .header-print {
            display: none; 
        }

        .logo {
            width: 100px; 
        }

        table thead {
            background-color: #343a40; 
            color: white; 
        }
    </style>
    <script>
        function printRow(rowId) {
            var row = document.getElementById(rowId);
            var table = row.closest('table');
            var headers = table.querySelector('thead').outerHTML;
            var rowHtml = row.outerHTML;
            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write('<style>@media print { .no-print { display: none; } }</style>'); 
            printWindow.document.write('</head><body>');
            printWindow.document.write('<div class="header-print">');
            printWindow.document.write('<img src="logo.png" alt="Pharmacy Logo" class="logo">'); 
            printWindow.document.write('<h1>Dabaay Pharmacy</h1>'); 
            printWindow.document.write('<p>Date: ' + new Date().toLocaleDateString() + '</p>'); 
            printWindow.document.write('<p>Contact Number: +252 617 083069</p>'); 
            printWindow.document.write('</div>');
            printWindow.document.write('<table border="1">' + headers + '<tbody>' + rowHtml + '</tbody></table>');
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
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
                <h2>Customer Report</h2><br><br> 
                    <!-- Search Form -->
                    <form method="POST" action="print_all.php">
                        <div class="input-group mb-3">
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" class="form-control" name="start_date">
                        </div>
                        <div class="input-group mb-3">
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" class="form-control" name="end_date">
                        </div>
                        <button type="submit" class="btn btn-success" value="Filter" name="filter">Filter</button><br><br>
                    </form>
                    <div class="table-responsive">
                        <?php
                        ini_set('display_errors', 1);
                        error_reporting(E_ALL);
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "pharmacy";
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        $start_date = $_POST['start_date'] ?? '';
                        $end_date = $_POST['end_date'] ?? '';
                        if (!empty($start_date) && !empty($end_date)) {
                            if ($start_date > $end_date) {
                                die("End date cannot be before start date.");
                            }
                            $sql = "SELECT * FROM customers 
                            WHERE created_at BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'";
                            $result = $conn->query($sql);
                            if ($conn->error) {
                                die("Query error: " . $conn->error);
                            }
                            $total_price = 0;
                            $total_positive_price = 0;
                            $total_subtotal_positive = 0;
                            if ($result->num_rows > 0) {
                                echo "<table border='1' class='table table-hover text-center'>
                                <thead>
                                <tr>
                                <th>ID</th>
                                <th>Age</th>
                                <th>Type of Disease</th>
                                <th>Type of Blood</th>
                                <th>Disease</th>
                                <th>Price</th>
                                <th>Type of Disease Positive</th>
                                <th>Quantity Positive</th>
                                <th>Positive Price</th>
                                <th>Subtotal Positive</th>
                                <th>Created At</th>
                                <th class='no-print'>Action</th>
                                </tr>
                                </thead>
                                <tbody>";
                                while ($row = $result->fetch_assoc()) {
                                    $total_price += $row["price"];
                                    $total_positive_price += $row["positive_price"];
                                    $total_subtotal_positive += $row["subtotal_positive"];

                                    $rowId = "row_" . $row["id"]; 
                                    echo "<tr id='$rowId'>
                                    <td>" . htmlspecialchars($row["id"]) . "</td>
                                    <td>" . htmlspecialchars($row["age"]) . "</td>
                                    <td>" . htmlspecialchars($row["type_disease"]) . "</td>
                                    <td>" . htmlspecialchars($row["type_blood"]) . "</td>
                                    <td>" . htmlspecialchars($row["disease"]) . "</td>
                                    <td>" . htmlspecialchars($row["price"]) . "</td>
                                    <td>" . htmlspecialchars($row["type_disease_positive"]) . "</td>
                                    <td>" . htmlspecialchars($row["quantity_positive"]) . "</td>
                                    <td>" . htmlspecialchars($row["positive_price"]) . "</td>
                                    <td>" . htmlspecialchars($row["subtotal_positive"]) . "</td>
                                    <td>" . htmlspecialchars($row["created_at"]) . "</td>
                                    <td class='no-print'><span onclick='printRow(\"$rowId\")' class='btn btn-success'><i class='fa fa-print'></i></span></td>
                                    </tr>";
                                }
                                echo "<tr>
                                <td colspan='5'><strong>Grand Totals:</strong></td>
                                <td><strong>" . number_format($total_price, 2) . "</strong></td>
                                <td colspan='2'></td>
                                <td><strong>" . number_format($total_positive_price, 2) . "</strong></td>
                                <td><strong>" . number_format($total_subtotal_positive, 2) . "</strong></td>
                                <td></td>
                                </tr>";
                                echo "</tbody></table>";
                            } else {
                                echo "No results found for the selected date range.";
                            }
                        } else {
                            echo "Please select both start and end dates.";
                        }
                        $conn->close();
                        ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
