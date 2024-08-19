<?php

session_start();

include './components/db_connect.php';
include "./components/customer_login.php";

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/customer_head.php"; ?>

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
                                <th><input type="checkbox" id="all" /></th>
                                <th>image</th>
                                <th>name</th>
                                <th>price</th>
                                <th>quantity</th>
                                <th>total price</th>
                                <th>Child</th>
                                <th>action</th>
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