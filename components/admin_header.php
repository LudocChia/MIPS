<nav>
    <div class="wrapper">
        <img src="../images/Mahans_IPS_logo.png" class="logo" alt="Mahans Internation Primary School logo">
        <div class="profile-area">
            <button id="menu-btn">
                <i class="bi bi-list"></i>
            </button>
            <div class="message-btn">
                <a href="notification.php"><i class="bi bi-bell-fill"></i></a>
            </div>
            <div class="profile">
                <!-- <div class="profile-photo">
                    <img src="../uploads/wangbingbing(1).png" alt="王冰冰">
                </div> -->
                <img src="../uploads/wangbingbing(1).png" alt="王冰冰" alt="" class="user-img" id="user-btn">
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
            </div>
        </div>
    </div>
</nav>