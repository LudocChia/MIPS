<header>
    <div class="wrapper">
        <a href="index.php">
            <img src="/mips/images/MIPS_logo.png" class="logo" alt="Mahans International Primary School logo">
        </a>
        <div class="profile-area">
            <button class="menu-btn">
                <i class="bi bi-list"></i>
            </button>
            <!-- <div class="message-btn">
                <a href="notification.php"><i class="bi bi-bell-fill"></i></a>
            </div> -->
            <div class="profile">
                <img src="<?= htmlspecialchars($_SESSION['admin_image']) ?>" alt="Admin Image" class="user-img" id="user-btn">
            </div>
        </div>
        <div class="profile-menu">
            <div class="user-info">
                <img src="<?= htmlspecialchars($_SESSION['admin_image']) ?>" alt="Admin Image">
                <h4><?php echo $_SESSION['admin_name']; ?></h4>
            </div>
            <hr>
            <!-- <a href="/mips/admin/profile.php" class="profile-menu-link"> -->
            <a href="javascript:void(0);" class="profile-menu-link">
                <i class="bi bi-person-fill"></i>
                <p>My Account</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="javascript:void(0);" class="profile-menu-link">
                <i class="bi bi-calendar-check"></i>
                <p>My Activities</p>
                <i class="bi bi-chevron-right"></i>
            </a>
            <a href="/mips/admin/logout.php" class="profile-menu-link">
                <i class="bi bi-box-arrow-right"></i>
                <p>Logout</p>
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>
    </div>
    <div class="mobile-wrapper">
        <div class="mobile-navbar">
            <ul>
                <li>
                    <a href="javascript:void(0);"><i class="fa fa-cutlery" aria-hidden="true"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);"><i class="bi bi-calendar4-event"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);"><i class="bi bi-shop-window"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);"><i class="bi bi-bell-fill"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);"><i class="bi bi-basket3-fill"></i></a>
                </li>
                <li>
                    <a href="javascript:void(0);" class="menu-btn"><i class="bi bi-list"></i></a>
                </li>
            </ul>
        </div>
    </div>
</header>