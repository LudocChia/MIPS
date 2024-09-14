<header>
    <div class="wrapper">
        <div class="logo">
            <a href="/mips">
                <img src="/mips/images/MIPS_logo.png" class="logo" alt="Mahans International Primary School logo">
            </a>
        </div>
        <div class="profile-area">
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <div class="profile">
                    <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="Admin Image" class="user-img" id="user-btn">
                    <div class="profile-menu">
                        <div class="user-info">
                            <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="User Image">
                            <h4><?= htmlspecialchars($_SESSION['user_name']); ?></h4>
                        </div>
                        <hr>
                        <a href="/mips/account.php" class="profile-menu-link">
                            <i class="bi bi-person-fill"></i>
                            <p>My Account</p>
                            <span>></span>
                        </a>
                        <a href="purchase.php" class="profile-menu-link">
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
    <!-- <div class="mobile-wrapper">
        <div class="mobile-navbar">
            <ul>
                <li>
                    <a href="meal.php"><i class="fa fa-cutlery" aria-hidden="true"></i></a>
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
    </div> -->
</header>