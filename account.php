<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = "My Account  - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";

?>

<body>
    <style>
        .profile-container {
            display: grid;
            grid-template-columns: 5fr 6fr;
            gap: 10rem;
            margin-top: 2rem;
        }

        .img-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .img-container img {
            /* width: 50%; */
            border-radius: 50%;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        @media screen and (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;

            }
        }
    </style>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container aside-main">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="profile">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Account</h1>
                        <p>Manage and protect your account</p>
                    </div>
                </div>
                <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="profile-container">
                        <div class="img-container">
                            <img src=<?php echo htmlspecialchars($_SESSION['user_image']); ?> alt="">
                            <input type="file" name="user_image" accept=".jpg, .jpeg, .png">
                            <p>File size: maximum 1 MB<br>File extension: .JPEG, .PNG</p>
                        </div>
                        <div class="info-container">
                            <div class="input-container">
                                <div class="input-field">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value='<?php echo htmlspecialchars($_SESSION['user_name']); ?>'>
                                </div>
                            </div>
                            <div class="input-container">
                                <div class="input-container">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" value='<?php echo htmlspecialchars($_SESSION['user_email']); ?>'>
                                </div>
                            </div>
                            <div class="input-container">
                                <div class="input-field">
                                    <label for="phone">Phone</label>
                                    <input type="text" name="phone" id="phone" value='<?php echo htmlspecialchars($_SESSION['user_phone']); ?>'>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>