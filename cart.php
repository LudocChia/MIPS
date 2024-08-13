<?php
include './components/db_connect.php';

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MIPS</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_internation_primary_school_logo.png">
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
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Shopping Cart</h1>
                    </div>
                    <div class="right">
                        <p>Total <b id="count"><?php echo count($cartItems); ?></b> Products</p>
                    </div>
                </div>
                <?php if ($isEmpty) : ?>
                    <div class="empty">
                        <img src='images/empty_cart.png' alt='Empty Cart Image'>
                        <h4>No Products Added</h4>
                        <p>Your cart is currently empty. Browse our selection and add items you like!</p>
                        <button class='empty'><a href='products.php'>Start Browsing</a></button>
                    </div>
                <?php else : ?>
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
                    <?php endif; ?>
                    </div>
            </div>
        </div>
    </section>
    <a href="#" id="todoBtn" class="listBtn">
        <span class="material-symbols-outlined">format_list_bulleted</span>
    </a>
    <div class="shoppingList">
        <div class="main-section">
            <div class="show-todo-section">
                <h3>Your Shopping List</h3>
                <div id="todo-items-container">
                    <!-- Shopping list items will be dynamically added here -->
                </div>
            </div>
            <div class="add-section">
                <form id="addForm" method="POST" autocomplete="off">
                    <input type="text" name="title" id="titleInput" placeholder="Add a grocery item?" required>
                    <button type="submit">Add</button>
                </form>
            </div>
        </div>
    </div>
    <a href="#" class="backToTop">
        <span class="material-symbols-outlined">arrow_upward</span>
    </a>
    <?php include './components/customer_footer.php'; ?>
    <?php echo "<script>var cartData = " . json_encode($cartItems) . ";</script>"; ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include 'components/alert.php'; ?>
    <script src="javascript/cart.js"></script>
    <script src="javascript/common.js"></script>
    <script type="text/javascript">
        var userData = <?php echo json_encode($user_data); ?>;
        (function(d, t) {
            var v = d.createElement(t),
                s = d.getElementsByTagName(t)[0];
            v.onload = function() {
                window.voiceflow.chat.load({
                    verify: {
                        projectID: '662e827c77f5f8be572ffdae'
                    },
                    url: 'https://general-runtime.voiceflow.com',
                    versionID: 'development',
                    launch: {
                        event: {
                            type: "launch",
                            payload: {
                                username: userData.customer_name,
                                userID: "<?php echo $customer_id; ?>",
                                email: userData.customer_email,
                                phone: userData.customer_phone,
                                registerDate: userData.register_datetime,
                                currentPage: 'shopping cart',
                                currentDate: "<?php echo $current_date; ?>",
                                currentWeekday: "<?php echo $current_weekday; ?>",
                                cartTotal: "<?php echo $totalPrice; ?>",
                                totalOrders: "<?php echo $orderCount; ?>",
                                wishlistID: "<?php echo $wishlist_id; ?>",
                                cartID: "<?php echo $cart_id; ?>"
                            }
                        }
                    }
                });
            }
            v.src = "https://cdn.voiceflow.com/widget/bundle.mjs";
            v.type = "text/javascript";
            s.parentNode.insertBefore(v, s);
        })(document, 'script');
    </script>
    <script>
        $(document).ready(function() {
            // Function to fetch and display the shopping list items
            function loadTodoItems() {
                $.ajax({
                    url: 'app/get_todos.php', // This should be a server-side script that returns the todo items in JSON format
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('.show-todo-section').empty(); // Clear existing items
                        $('.show-todo-section').append('<h3>Your Shopping List</h3>'); // Re-add the title

                        if (data.length === 0) {
                            $('.show-todo-section').append('<div class="empty"><img src="images/empty_cart.png" /><h4>No Items in Shopping List</h4><p>Check back later or add new items!</p></div>');
                        } else {
                            data.forEach(function(todo) {
                                const checked = todo.checked ? 'checked' : '';
                                const checkedClass = todo.checked ? 'class="checked"' : '';
                                $('.show-todo-section').append(`
                            <div class="todo-item">
                                <span id="${todo.shopping_item_id}" class="remove-to-do">x</span>
                                <input type="checkbox" class="check-box" data-todo-id="${todo.shopping_item_id}" ${checked} />
                                <h2 ${checkedClass}>${todo.shopping_list_name}</h2>
                            </div>
                        `);
                            });
                            attachEventListeners(); // Re-attach event listeners after DOM update
                        }
                    }
                });
            }

            // Function to attach event listeners to dynamically added items
            function attachEventListeners() {
                $('.remove-to-do').off('click').on('click', function() {
                    const id = $(this).attr('id');
                    $.post("app/remove.php", {
                        id: id
                    }, (data) => {
                        if (data) {
                            loadTodoItems(); // Reload items after removal
                        }
                    });
                });

                $('.check-box').off('click').on('click', function() {
                    const id = $(this).attr('data-todo-id');
                    $.post('app/check.php', {
                        id: id
                    }, (data) => {
                        if (data != 'error') {
                            loadTodoItems(); // Reload items after check/uncheck
                        }
                    });
                });
            }

            // Initial load of todo items
            loadTodoItems();

            // Handle form submission for adding new todo items
            $('#addForm').submit(function(e) {
                e.preventDefault();
                const title = $('#titleInput').val();
                $.ajax({
                    type: 'POST',
                    url: 'app/add.php',
                    data: {
                        title: title
                    },
                    success: function() {
                        $('#titleInput').val(''); // Clear the input field
                        loadTodoItems();
                    }
                });
            });
        });
    </script>
</body>

</html>