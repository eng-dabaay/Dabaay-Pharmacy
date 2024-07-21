<?php

session_start();
include "./database/db.php";

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function getAvailableQuantity($product_id, $conn) {
    $query = "SELECT quantity FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['quantity'] ?? 0;
}

if (isset($_GET['add'])) {
    $product_id = $_GET['add'];
    $query = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        
        if (!isset($_SESSION['cart'][$product_id])) {
            $product['quantity'] = 1;
        }

        $_SESSION['cart'][$product_id] = $product;
    }

    $stmt->close();
}

if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
}

if (isset($_POST['save_order'])) {
    
    $insertQuery = "INSERT INTO orderlist (product_name, image, price, quantity, total) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);

    foreach ($_SESSION['cart'] as $product_id => $product) {
        $product_name = $product['product_name'];
        $image = $product['image'];
        $price = $product['price'];
        $quantity = $_POST['quantity'][$product_id]; 
        $total = $price * $quantity;

        $stmt->bind_param("ssidi", $product_name, $image, $price, $quantity, $total);
        $stmt->execute();
    }

    $stmt->close();

    unset($_SESSION['cart']);

    header("Location: OrderList.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "header.php"; ?>
    <style>
        .card-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .card {
            flex: 1;
            max-width: 300px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
        }
        .quantity-control button {
            margin: 0 5px;
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
                    <h2>Your Cart</h2>
                    <div class="card-group">
                        <?php
                        if (!empty($_SESSION['cart'])) {
                            echo "<form method='post'>";
                            $cart_total = 0;
                            foreach ($_SESSION['cart'] as $product_id => $product) {
                                $available_quantity = getAvailableQuantity($product_id, $conn);
                                $cart_total += $product['price'] * $product['quantity'];
                                echo "<div class='card'>";
                                if ($product["image"]) {
                                    echo '<img src="uploads/' . $product["image"] . '" class="card-img-top" alt="Product Image" style="max-height: 200px; object-fit: cover;">';
                                } else {
                                    echo '<img src="path/to/default-image.jpg" class="card-img-top" alt="No Image Available" style="max-height: 200px; object-fit: cover;">';
                                }
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . htmlspecialchars($product["product_name"]) . "</h5>";
                                echo "<p class='card-text'>Price: $" . number_format($product["price"], 2) . "</p>";

                                echo "<div class='quantity-control'>";
                                echo "<button type='button' class='btn btn-secondary btn-sm' onclick='updateQuantity(" . $product_id . ", -1, " . $available_quantity . ")'>-</button>";
                                echo "<input type='number' id='quantity_" . $product_id . "' name='quantity[" . $product_id . "]' min='1' max='" . $available_quantity . "' value='" . $product['quantity'] . "' class='form-control mb-2' data-price='" . $product['price'] . "' onchange='updateTotal(" . $product_id . "," . $product['price'] . ")'>";
                                echo "<button type='button' class='btn btn-secondary btn-sm' onclick='updateQuantity(" . $product_id . ", 1, " . $available_quantity . ")'>+</button>";
                                echo "</div>";

                                echo "<p class='card-text'>Total: <span id='total_" . $product_id . "'>$" . number_format($product["price"] * $product["quantity"], 2) . "</span></p>";

                                echo "<form method='post'>";
                                echo "<button type='submit' class='btn btn-success' name='save_order' value='1'>Save</button>&nbsp;&nbsp;";

                                echo "<a href='cart.php?remove=" . htmlspecialchars($product_id) . "' class='btn btn-danger'>Remove</a>&nbsp;&nbsp;";
                                echo "<a href='#' class='btn btn-info' onclick='window.print();'>Print</a>";
                                echo "</form>";
                                echo "</div>";
                                echo "</div>";
                            }
                            // echo "<div><strong>Cart Total: $<span id='cart_total'>" . number_format($cart_total, 2) . "</span></strong></div>";
                            echo "</form>";
                        } else {
                            echo "<p>Your cart is empty</p>";
                        }
                        ?>
                    </div>
                </div>
            </main>
            <?php include "footer.php"; ?>
        </div>
    </div>

    <script>
        function updateQuantity(productId, change, maxQuantity) {
            let quantityInput = document.getElementById('quantity_' + productId);
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
                newQuantity = 1;
            } else if (newQuantity > maxQuantity) {
                newQuantity = maxQuantity;
            }

            quantityInput.value = newQuantity;
            updateTotal(productId, parseFloat(quantityInput.getAttribute('data-price')));
            updateCartTotal();
        }

        function updateTotal(productId, price) {
            let quantity = parseInt(document.getElementById('quantity_' + productId).value);
            let total = quantity * price;
            document.getElementById('total_' + productId).textContent = '$' + total.toFixed(2);
        }

        function updateCartTotal() {
            let cartTotal = 0;
            const quantityInputs = document.querySelectorAll("input[name^='quantity']");
            quantityInputs.forEach(input => {
                let productId = input.id.split('_')[1];
                let price = parseFloat(input.getAttribute('data-price'));
                let quantity = parseInt(input.value);
                cartTotal += price * quantity;
            });
            document.getElementById('cart_total').textContent = cartTotal.toFixed(2);
        }
    </script>
</body>
</html>
