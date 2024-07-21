<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sb-sidenav {
            width: 220px; 
        }
        .sb-nav-link-icon {
            margin-right: 10px;
        }
        .dropdown-menu {
            background-color: transparent;
            border: none; 
        }
        .dropdown-item {
            color: white; 
            display: flex;
            justify-content: center; 
            align-items: center; 
            text-align: center; 
        }
        .dropdown-item:hover {
            background-color: transparent; 
            color: white; 
        }
    </style>
</head>
<body>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <br>
                        <a class="nav-link" href="Orders.php">
                            <div class="sb-nav-link-icon"><img width="28" height="28" src="https://img.icons8.com/color/48/doctors-bag.png" alt="doctors-bag"/></div>
                            Orders Medicine
                        </a><br>
                        <a class="nav-link" href="OrderList.php">
                            <div class="sb-nav-link-icon"><img width="28" height="28" src="https://img.icons8.com/color-glass/48/sell.png" alt="sell"/></div>
                            Sell Order
                        </a><br>
                        <a class="nav-link" href="BuyMedicine.php">
                            <div class="sb-nav-link-icon"><img width="27" height="27" src="https://img.icons8.com/forma-regular-filled/24/FAB005/buy.png" alt="buy"/></div>
                            Buy Medicine
                        </a><br>
                        <a class="nav-link" href="Customer.php">
                            <div class="sb-nav-link-icon"><img width="28" height="28" src="https://img.icons8.com/color/48/gender-neutral-user.png" alt="gender-neutral-user"/></div>
                            Customer
                        </a><br>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="sb-nav-link-icon"><img width="28" height="28" src="https://img.icons8.com/color/48/business-report.png" alt="business-report"/></div>
                                Reports
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" href="Report.php">Order Report</a></li>
                                <li><a class="dropdown-item" href="print_all.php">Customer Report</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
