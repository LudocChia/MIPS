<header>
    <div class="wrapper">
        <div class="logo">
            <a href="/mips">
                <img src="/mips/images/MIPS_logo.png" class="logo" alt="Mahans International Primary School logo">
            </a>
        </div>
        <!-- <span class="fas fa-bars" id="menuIcon" onclick="toggle()"></span> -->
        <div class="navbar" id="nav">
            <ul>
                <!-- <li>
                    <a href="index.php"><span class="material-symbols-outlined icon-adjust">local_library</span></i>Meal Donation</a>
                </li> -->
                <li>
                    <a href="/mips/parent/donationMain.php"><i class="fa fa-cutlery" aria-hidden="true"></i>Meal Donation</a>
                </li>
                <!-- <li>
                    <a href="event.php"><i class="bi bi-calendar4-event"></i>School Event</a>
                </li> -->
                <li>
                    <a href="/mips/bookshop.php"><i class="bi bi-shop-window"></i>Bookshop</a>
                </li>
                <li>
                    <a href="/mips/application.php"><i class="bi bi-list"></i>Job application</a>
                </li>
            </ul>
        </div>
        <div class="profile-area">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- <a href="mailbox.php"><i class="bi bi-bell-fill"></i> -->
                <!-- <sup id="MessageCount"></?= $total_unread_messages > 0 ? $total_unread_messages : '0' ?></sup> -->
                <!-- </a> -->
                <a href="cart.php"><i class="bi bi-basket3-fill"></i>
                    <!-- <sup id="cartCount"></?= $total_cart_items > 0 ? $total_cart_items : '0' ?></sup> -->
                </a>
                <div class="profile">
                    <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="User Image" class="user-img" id="user-btn">
                </div>
            <?php else: ?>
                <button class="btn login" id="login-btn">Login</button>
                <a href="/mips/login.php">
                    <img src="/mips/images/default_profile.png" alt="User Image" class="user-img login" id="user-btn">
                </a>
            <?php endif; ?>
        </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="profile-menu">
                <div class="user-info">
                    <img src="<?= htmlspecialchars($_SESSION['user_image']) ?>" alt="User Image">
                    <h4><?= htmlspecialchars($_SESSION['user_name']); ?></h4>
                </div>
                <hr>
                <a href="javascript:void(0)" class="profile-menu-link">
                    <i class="bi bi-person-fill"></i>
                    <p>My Account</p>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="/mips/parent/donationHistory.php" class="profile-menu-link">
                    <i class="bi bi-calendar-check"></i>
                    <p>My Activities</p>
                    <i class="bi bi-chevron-right"></i>
                </a>
                <a href="/mips/logout.php" class="profile-menu-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <p>Logout</p>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="mobile-wrapper">
        <div class="mobile-navbar">
            <ul>
                <li>
                    <a href="/mips/parent/donationMain.php"><i class="fa fa-cutlery" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><i class="bi bi-calendar4-event"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><i class="bi bi-shop-window"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><i class="bi bi-bell-fill"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><i class="bi bi-basket3-fill"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0)"><i class="bi bi-list"></i></a>
                </li>
            </ul>
        </div>
    </div>
</header>