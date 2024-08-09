<header>
    <div class="wrapper">
        <div class="logo">
            <a href="index.php">
                <img src="./images/Mahans_IPS_logo.png" class="logo" alt="Mahans Internation Primary School logo">
            </a>
        </div>
        <span class="fas fa-bars" id="menuIcon" onclick="toggle()"></span>
        <div class="navbar" id="nav">
            <ul>
                <li>
                    <a href="bookshop.php">Bookshop</a>
                </li>
            </ul>
        </div>
        <div>
            <div class="profile-area">
                <?php
                if (isset($_SESSION['customer_id'])) {
                    $customer_id = $_SESSION['customer_id'];

                    $cart_query = $pdo->prepare("SELECT cart_id FROM Cart WHERE parent_student_id = ?");
                    $cart_query->execute([$customer_id]);
                    $cart_id = $cart_query->fetchColumn();

                    $total_cart_items = 0;
                    if ($cart_id) {
                        $cart_items_count_query = $pdo->prepare("SELECT COUNT(DISTINCT product_id) FROM Cart_Item WHERE cart_id = ?");
                        $cart_items_count_query->execute([$cart_id]);
                        $total_cart_items = $cart_items_count_query->fetchColumn();
                    }

                    $select_profile = $pdo->prepare("SELECT customer_image FROM Customer WHERE customer_id = ?");
                    $select_profile->execute([$customer_id]);

                    if ($select_profile->rowCount() > 0) {
                        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                        $user_image_path = !empty($fetch_profile['customer_image']) ? "images/" . $fetch_profile['customer_image'] : 'images/defaultprofile.png';
                ?>
                        <a href="mailbox.php"><i class="bi bi-bell-fill"></i>
                            <sup id="MessageCount"><?= $total_unread_messages > 0 ? $total_unread_messages : '0' ?></sup>
                        </a>
                        <a href="myFavorite.php"><i class="bi bi-heart-fill"></i>
                            <sup id="wishlistCount"><?= $total_wishlist_items > 0 ? $total_wishlist_items : '0' ?></sup>
                        </a>

                        <a href="cart.php"><i class="bi bi-basket3-fill"></i>
                            <sup id="cartCount"><?= $total_cart_items > 0 ? $total_cart_items : '0' ?></sup>
                        </a>
                        <div class="profile">
                            <img src="<?= htmlspecialchars($user_image_path) ?>" alt="User Image" class="user-img" id="user-btn">
                            <div class="profile-menu">
                                <div class="user-info">
                                    <img src="<?= htmlspecialchars($user_image_path) ?>" alt="User Image">
                                    <h4><?= htmlspecialchars($_SESSION['customer_name']); ?></h4>
                                </div>
                                <hr>
                                <a href="profile.php" class="profile-menu-link">
                                    <i class="bi bi-person-fill"></i>
                                    <p>My Account</p>
                                    <span>></span>
                                </a>
                                <a href="order_history.php?filter=all" class="profile-menu-link">
                                    <i class="bi bi-calendar-check"></i>
                                    <p>My Activities</p>
                                    <span>></span>
                                </a>
                                <a href="logout.php" class="profile-menu-link">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <p>Logout</p>
                                    <span>></span>
                                </a>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <a href="login.php" class="btn login">Login</a>
                    <a href="register.php" class="btn signup">Sign Up</a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</header>