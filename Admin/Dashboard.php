<?php
include "./database/db.php";

// Fetch the count of orders
$query_orders = "SELECT COUNT(*) as total_orders FROM orders";
$stmt_orders = $conn->prepare($query_orders);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$row_orders = $result_orders->fetch_assoc();
$total_orders = $row_orders['total_orders'];
$stmt_orders->close();

// Fetch the count of customers
$query_customers = "SELECT COUNT(*) as total_customers FROM customers";
$stmt_customers = $conn->prepare($query_customers);
$stmt_customers->execute();
$result_customers = $stmt_customers->get_result();
$row_customers = $result_customers->fetch_assoc();
$total_customers = $row_customers['total_customers'];
$stmt_customers->close();

// Fetch the count of medicines
$query_medicines = "SELECT COUNT(*) as total_medicines FROM medicine";
$stmt_medicines = $conn->prepare($query_medicines);
$stmt_medicines->execute();
$result_medicines = $stmt_medicines->get_result();
$row_medicines = $result_medicines->fetch_assoc();
$total_medicines = $row_medicines['total_medicines'];
$stmt_medicines->close();

// Fetch the count of orderlists
$query_orderlists = "SELECT COUNT(*) as total_orderlists FROM orderlist";
$stmt_orderlists = $conn->prepare($query_orderlists);
$stmt_orderlists->execute();
$result_orderlists = $stmt_orderlists->get_result();
$row_orderlists = $result_orderlists->fetch_assoc();
$total_orderlists = $row_orderlists['total_orderlists'];
$stmt_orderlists->close();

// Fetch the count of users
$query_users = "SELECT COUNT(*) as total_users FROM users";
$stmt_users = $conn->prepare($query_users);
$stmt_users->execute();
$result_users = $stmt_users->get_result();
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total_users'];
$stmt_users->close();

// Fetch the count of buy medicine
$query_buymedicine = "SELECT COUNT(*) as total_buymedicine FROM buymedicine";
$stmt_buymedicine = $conn->prepare($query_buymedicine);
$stmt_buymedicine->execute();
$result_buymedicine = $stmt_buymedicine->get_result();
$row_buymedicine = $result_buymedicine->fetch_assoc();
$buymedicines = $row_buymedicine['total_buymedicine'];
$stmt_buymedicine->close();

// Fetch the total subtotal from the buymedicine table
$query_total_subtotal = "SELECT SUM(subtotal) as total_subtotal FROM buymedicine";
$stmt_total_subtotal = $conn->prepare($query_total_subtotal);
$stmt_total_subtotal->execute();
$result_total_subtotal = $stmt_total_subtotal->get_result();
$row_total_subtotal = $result_total_subtotal->fetch_assoc();
$total_subtotal = $row_total_subtotal['total_subtotal'];
$stmt_total_subtotal->close();


// Fetch the total subtotal from the orderlist table
$query_orderlist_subtotal = "SELECT SUM(total) as orderlist_subtotal FROM orderlist";
$stmt_orderlist_subtotal = $conn->prepare($query_orderlist_subtotal);
$stmt_orderlist_subtotal->execute();
$result_orderlist_subtotal = $stmt_orderlist_subtotal->get_result();
$row_orderlist_subtotal = $result_orderlist_subtotal->fetch_assoc();
$orderlist_subtotal = $row_orderlist_subtotal['orderlist_subtotal'];
$stmt_orderlist_subtotal->close();


// Fetch all data for each table
$customers_query = "SELECT * FROM customers";
$orders_query = "SELECT * FROM orders";
$orderlists_query = "SELECT * FROM orderlist";
$medicines_query = "SELECT * FROM medicine";
$users_query = "SELECT * FROM users";
$buymedicine_query = "SELECT * FROM buymedicine";

$customers = $conn->query($customers_query);
$orders = $conn->query($orders_query);
$orderlists = $conn->query($orderlists_query);
$medicines = $conn->query($medicines_query);
$users = $conn->query($users_query);
$buymedicine = $conn->query($buymedicine_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        .large-count {
            font-size: 2em; 
            font-weight: bold;
        }
        .table-container {
            margin-top: 20px;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-container th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <?php include "navbar.php"; ?>
    <div id="layoutSidenav">
        <?php include "sidebar.php"; ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Orders</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo $total_orders; ?> Orders</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-info text-white mb-4">
                                <div class="card-body">OrderList</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo $total_orderlists; ?> Order Lists</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-warning text-white mb-4">
                                <div class="card-body">Customers</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo $total_customers; ?> Customers</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Medicines</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo $total_medicines; ?> Medicines</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-black text-white mb-4">
                                <div class="card-body">Users</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo $total_users; ?> Users</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-success text-white mb-4">
                                <div class="card-body">Amount Buying</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo number_format($total_subtotal, 2); ?> Amount</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-primary text-white mb-4">
                                <div class="card-body">Amount Seller</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white"><?php echo number_format($orderlist_subtotal, 2); ?> Amount</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card bg-danger text-white mb-4">
                                <div class="card-body">Reports</div>
                                <div class="card-footer d-flex align-items-center justify-content-between">
                                    <span class="large-count text-white">Reports</span>
                                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Display Tables -->
                    <div class="table-container">
                        <h2>Customers</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Age</th>
                                    <th>Type of Disease</th>
                                    <th>Blood</th>
                                    <th>Disease Type</th>
                                    <th>Weight (kg)</th>
                                    <th>Height (cm)</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $customers->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tel']); ?></td>
                                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                                        <td><?php echo htmlspecialchars($row['type_disease']); ?></td>
                                        <td><?php echo htmlspecialchars($row['type_blood']); ?></td>
                                        <td><?php echo htmlspecialchars($row['disease']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kg']); ?></td>
                                        <td><?php echo htmlspecialchars($row['cm']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h2>Orders</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>OrderName</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                    <th>Image</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total']); ?></td>
                                        <td><?php echo htmlspecialchars($row['image']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h2>Order List</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>OrderName</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Image</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $orderlists->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                        <td><?php echo htmlspecialchars($row['total']); ?></td>
                                        <td><?php echo htmlspecialchars($row['image']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h2>Medicines</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $medicines->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['qty']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h2>Users</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fullname</th>
                                    <th>Phone No</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tell']); ?></td>
                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-container">
                        <h2>Buy Medicine</h2>
                        <table class="table table-bordered table-hover text-center">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Medicine Name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $buymedicine->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['qty']); ?></td>
                                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                                        <td><?php echo htmlspecialchars($row['subtotal']); ?></td>
                                        <td><?php echo htmlspecialchars($row['date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </main>
        </div>
    </div>
    <script src="path/to/bootstrap.bundle.js"></script> 
</body>
</html>
