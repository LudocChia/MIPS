<?php
$default_logo_path = "../images/Mahans_IPS_logo.png";
$logo_path = "../../images/Mahans_IPS_logo.png";

$deafault_admin_profile_path = "../uploads/admin/";
$admin_profile_path = "../../uploads/admin/";

if (file_exists($logo_path) && !empty($logo_path)) {
    $src = $logo_path;
} else {
    $src = $default_logo_path;
}
?>

<header>
    <div class="wrapper">
        <a href="index.php">
            <img src="<?= $src ?>" class="logo" alt="Mahans International Primary School logo">
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
                    <a href="logout.php" class="profile-menu-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>