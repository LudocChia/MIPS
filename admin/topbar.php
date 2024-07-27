<header>
    <div class="header">
        <div class="wrapper">
            <div class="logo">
                <a href="index.php">
                    <img src="../images/Mahans_IPS_logo.png" alt="Mahans Internation Primary School logo">
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
                <div class="user">
                    <?php
                    if (isset($_SESSION['customer_name'])) {
                        $select_profile = $pdo->prepare("SELECT * FROM `customer` WHERE customer_id = ?");
                        $select_profile->execute([$customer_id]);

                        $unread_messages_query = $pdo->prepare("SELECT COUNT(*) FROM Notification WHERE customer_id = ? AND status = 'unread' AND is_deleted = 0");
                        $unread_messages_query->execute([$_SESSION['customer_id']]);
                        $total_unread_messages = $unread_messages_query->fetchColumn();

                        $wishlist_query = $pdo->prepare("SELECT wishlist_id FROM Wishlist WHERE customer_id = ?");
                        $wishlist_query->execute([$_SESSION['customer_id']]);
                        $wishlist_id = $wishlist_query->fetchColumn();

                        $total_wishlist_items = 0;

                        if ($wishlist_id) {
                            $wishlist_items_count_query = $pdo->prepare("SELECT COUNT(*) FROM Wishlist_Item WHERE wishlist_id = ?");
                            $wishlist_items_count_query->execute([$wishlist_id]);
                            $total_wishlist_items = $wishlist_items_count_query->fetchColumn();
                        }

                        $cart_query = $pdo->prepare("SELECT cart_id FROM Cart WHERE customer_id = ?");
                        $cart_query->execute([$_SESSION['customer_id']]);
                        $cart_id = $cart_query->fetchColumn();

                        $total_cart_items = 0;

                        if ($cart_id) {
                            $cart_items_count_query = $pdo->prepare("SELECT COUNT(DISTINCT product_id) FROM Cart_Item WHERE cart_id = ?");
                            $cart_items_count_query->execute([$cart_id]);
                            $total_cart_items = $cart_items_count_query->fetchColumn();
                        }

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
                            <i><img src="<?= $user_image_path; ?>" alt="" class="user-img" id="user-btn"></i>
                            <div class="profile-menu">
                                <div class="user-info">
                                    <img src="<?= $user_image_path; ?>" alt="">
                                    <h4><?php echo $_SESSION['customer_name']; ?></h4>
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
                        <?php
                        }
                    } else {
                        ?>
                        <!-- <a href="login.php" class="btn login">Login</a>
                        <a href="register.php" class="btn signup">Sign Up</a> -->
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
</header>