<?php

session_start();

include './components/db_connect.php';
include "./components/customer_login.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MIPS</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_IPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/customer.css">
</head>

<body>
    <?php include './components/customer_header.php'; ?>
    <section class="shopping-cart">
        <div class="container">
            <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Shopping Cart</h1>
                    </div>
                    <div class="right">
                        <!-- <p>Total <b id="count"></?php echo count($cartItems); ?></b> Products</p> -->
                    </div>
                </div>
                <!-- </?php if ($isEmpty) : ?>
                    <div class="empty">
                        <img src='images/empty_cart.png' alt='Empty Cart Image'>
                        <h4>No Products Added</h4>
                        <p>Your cart is currently empty. Browse our selection and add items you like!</p>
                        <button class='empty'><a href='products.php'>Start Browsing</a></button>
                    </div> -->
                <!-- </?php else : ?> -->
                <div class="table-cart-items">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th><input type="checkbox" id="all" /></th>
                                <th>image</th>
                                <th>name</th>
                                <th>price</th>
                                <th>quantity</th>
                                <th>total price</th>
                                <th>action</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="order-items">
                            <!-- list of products -->
                        </tbody>
                    </table>
                    <div class="controls clearfix">
                        <input type="checkbox" id="selectAll" class="select-all">
                        <label for="selectAll">Select All Products</label>
                        <a href="javascript:" class="del-all">Delete Selected Products</a>
                        <a href="javascript:" class="clear">Delete All Products</a>
                        <p>Total ( <span id="totalCount">0</span> item s ): RM <span id="totalPrice" class="total-price">0.00</span></p>
                        <button class="pay">Checkout</button>
                    </div>
                    <!-- </?php endif; ?> -->
                </div>
            </div>
        </div>
    </section>
    <!-- <a href="#" class="backToTop">
        <span class="material-symbols-outlined">arrow_upward</span>
    </a> -->
    <?php include "./components/customer_footer.php"; ?>
    <script src="./javascript/common.js"></script>
    <script src="./javascript/customer.js"></script>
    <script src="./javascript/cart.js"></script>
</body>

</html>