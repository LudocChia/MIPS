<head>
    <link rel="icon" type="image/x-icon" href="../../images/MIPS_icon.png">
</head>

<header>
    <div class="wrapper">
        <a href="index.php">
            <img src="../../images/MIPS_logo.png" class="logo" alt="Mahans International Primary School logo">
        </a>
        <div class="profile-area">
            <button id="menu-btn">
                <i class="bi bi-list"></i>
            </button>
            <div class="message-btn">
                <a href="notification.php"><i class="bi bi-bell-fill"></i></a>
            </div>
            <div class="profile">
                <img src="<?= htmlspecialchars($_SESSION['admin_image']) ?>" alt="Admin Image" class="user-img" id="user-btn">
                <div class="profile-menu">
                    <div class="user-info">
                        <img src="<?= htmlspecialchars($_SESSION['admin_image']) ?>" alt="Admin Image">
                        <h4><?php echo $_SESSION['admin_name']; ?></h4>
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
                    <a href="/mips/admin/logout.php" class="profile-menu-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>