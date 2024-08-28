<?php
session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

$pageTitle = "Shopping Cart - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";

$parentId = $_SESSION['user_id'] ?? null;
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="shopping-cart">
        <input type="hidden" id="user-id" value="<?php echo htmlspecialchars($parentId); ?>">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Shopping Cart</h1>
                    </div>
                    <div class="right">
                        <p>Total <b id="count">0</b> Products</p>
                    </div>
                </div>

                <div id="cart-content">
                    <div class="empty">
                        <img src='images/empty_cart.png' alt='Empty Cart Image'>
                        <h3>No Products Added</h3>
                        <a href="/mips/bookshop.php"><button class='btn btn-outline-primary'>Start Browsing</button></a>
                    </div>
                </div>

                <div id="cart-items" style="display:none;">
                    <div class="table-cart-items">
                        <table>
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="all" /></th>
                                    <th style="width: 10%;">Image</th>
                                    <th style="width: 20%;">Name</th>
                                    <th style="width: 10%;">Size</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 15%;">Total Price</th>
                                    <th style="width: 20%;">Child</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="order-items"></tbody>
                        </table>
                        <div class="controls clearfix">
                            <input type="checkbox" id="selectAll" class="select-all">
                            <label for="selectAll">Select All Products</label>
                            <a href="javascript:" class="delete-selected">Delete Selected Products</a>
                            <a href="javascript:" class="clear">Delete All Products</a>
                            <p>Total (<span id="totalCount">0</span> items): RM <span id="totalPrice" class="total-price">0.00</span></p>
                            <button class="pay">Checkout</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="right">
                <h1>Purchase Product</h1>
            </div>
            <div class="left">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product-id" value="">
            <input type="hidden" name="size_id" id="size-id" value="">
            <input type="hidden" name="product_price" id="product-price" value="">

            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Size</th>
                        <th>Child</th>
                        <th>Price (RM)</th>
                    </tr>
                </thead>
                <tbody id="checkout-items-body">
                </tbody>
            </table>

            <div class="input-container">
                <div class="input-field">
                    <h2>Total Price (RM)</h2>
                    <input type="text" name="total_price_display" id="total-price-display" value="0.00" readonly>
                </div>
            </div>
            <div class="input-container">
                <h2>Payment Method</h2>
                <h3>Kindly make payment via online banking. Bank details are as follows:</h3>
                <table class="two-column">
                    <tr>
                        <td style="width: 40%"><strong>Beneficiary :</strong></td>
                        <td style="width: 60%">mips International Sdn Bhd</td>
                    </tr>
                    <tr>
                        <td style="width: 40%"><strong>Name of Bank :</strong></td>
                        <td style="width: 60%">Public Islamic Bank</td>
                    </tr>
                    <tr>
                        <td style="width: 40%"><strong>Bank Address :</strong></td>
                        <td style="width: 60%">39, 40 & 41 Lorong Setia Satu, Ayer Keroh Heights, 75450 Melaka.</td>
                    </tr>
                    <tr>
                        <td style="width: 40%"><strong>Account Number :</strong></td>
                        <td style="width: 60%">3818938926</td>
                    </tr>
                    <tr>
                        <td style="width: 40%"><strong>Swift CODE :</strong></td>
                        <td style="width: 60%">PBBEMYKL</td>
                    </tr>
                </table>
            </div>

            <div class="input-container">
                <div class="input-field">
                    <h2>Upload Transfer Receipt<sup>*</sup></h2>
                    <input type="file" name="payment_image" accept=".jpg, .jpeg, .png" required>
                </div>
                <p>Please upload the transfer receipt.</p>
            </div>
            <div class="input-container controls">
                <button value="cancel" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Purchase</button>
            </div>
        </form>
    </dialog>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/temp.js"></script>
</body>

</html>