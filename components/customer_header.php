<header>
    <div class="wrapper">
        <div class="logo">
            <a href="/mips">
                <img src="/mips/images/MIPS_logo.png" class="logo" alt="Mahans International Primary School logo">
            </a>
        </div>
        <span class="fas fa-bars" id="menuIcon" onclick="toggle()"></span>
        <div class="navbar" id="nav">
            <ul>
                <!-- <li>
                    <a href="index.php"><span class="material-symbols-outlined icon-adjust">local_library</span></i>Meal Donation</a>
                </li> -->
                <li>
                    <a href="meal.php"><span class="material-symbols-outlined icon-adjust">food_bank</span>Meal Donation</a>
                </li>
                <!-- <li>
                    <a href="event.php"><i class="bi bi-calendar4-event"></i>School Event</a>
                </li> -->
                <li>
                    <a href="bookshop.php"><i class="bi bi-shop-window"></i>Bookshop</a>
                </li>
            </ul>
        </div>
        <div>
            <div class="profile-area">
                <?php
                if (isset($_SESSION['user_id'])) {
                ?>
                    <a href="mailbox.php"><i class="bi bi-bell-fill"></i>
                        <!-- <sup id="MessageCount"></?= $total_unread_messages > 0 ? $total_unread_messages : '0' ?></sup> -->
                    </a>
                    <a href="cart.php"><i class="bi bi-basket3-fill"></i>
                        <!-- <sup id="cartCount"></?= $total_cart_items > 0 ? $total_cart_items : '0' ?></sup> -->
                    </a>
                    <div class="profile">
                        <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="Admin Image" class="user-img" id="user-btn">
                        <div class="profile-menu">
                            <div class="user-info">
                                <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="User Image">
                                <h4><?= htmlspecialchars($_SESSION['user_name']); ?></h4>
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
                            <a href="/mips/logout.php" class="profile-menu-link">
                                <i class="bi bi-box-arrow-right"></i>
                                <p>Logout</p>
                                <span>></span>
                            </a>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <button class="btn login" id="login-btn">Login</button>
                    <!-- <button class="btn btn-outline-primary" id="signup-btn">Sign Up</button> -->
                    <a href="/mips/login.php"><img src="/mips/images/default_profile.png" alt="User Image" class="user-img login" id="user-btn"></a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
    <div class="mobile-wrapper">
        <div class="mobile-navbar">
            <ul>
                <li>
                    <a href="meal.php"><span class="material-symbols-outlined icon-adjust">food_bank</span></a>
                </li>
                <li>
                    <a href="event.php"><i class="bi bi-calendar4-event"></i></a>
                </li>
                <li>
                    <a href="bookshop.php"><i class="bi bi-shop-window"></i></a>
                </li>
                <li>
                    <a href="mailbox.php"><i class="bi bi-bell-fill"></i></a>
                </li>
                <li>
                    <a href="cart.php"><i class="bi bi-basket3-fill"></i></a>
                </li>
                <li>
                    <a href="menu.php"><i><i class="bi bi-list"></i></i></a>
                </li>
            </ul>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userBtn = document.querySelector("#user-btn");
        const profileMenu = document.querySelector(".profile-menu");

        if (userBtn && profileMenu) {
            userBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                profileMenu.classList.toggle('active');
            });

            document.addEventListener('click', function(event) {
                if (isAutoSliding) return;
                if (!profileMenu.contains(event.target) && !userBtn.contains(event.target)) {
                    profileMenu.classList.remove('active');
                }
            });

            window.addEventListener('resize', function() {
                if (profileMenu.classList.contains('active')) {
                    profileMenu.classList.remove('active');
                }
            });
        }

        const burgerMenu = document.getElementById("burger-menu");
        const navMenu = document.getElementById("nav-menu");
        const searchContainer = document.getElementById("search-container");

        if (burgerMenu && navMenu && searchContainer) {
            burgerMenu.addEventListener("click", function() {
                navMenu.classList.toggle("active");
                searchContainer.classList.toggle("active");
            });
        }

        const menuIcon = document.getElementById("menuIcon");
        const nav = document.getElementById("nav");

        if (menuIcon && nav) {
            menuIcon.addEventListener("click", function() {
                console.log('menuIcon clicked');
                nav.classList.toggle("navactive");
                console.log('nav class list:', nav.classList);
            });

            document.addEventListener("click", function(event) {
                if (!nav.contains(event.target) && !menuIcon.contains(event.target)) {
                    nav.classList.remove("navactive");
                }
            });
        }
    });
</script>