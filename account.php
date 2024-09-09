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
        .profile .wrapper {
            background-color: white;
        }

        .profile-container {
            display: grid;
            grid-template-columns: 5fr 6fr;
            gap: 10rem;
            margin-top: 2rem;
        }

        .img-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            /* Adjust spacing between elements */
        }

        .img-container img {
            /* width: 50%; */
            border-radius: 50%;
        }

        input[type="file"] {
            display: none;
        }

        label {
            display: inline-block;
            text-transform: uppercase;
            background: #c0392b;
            color: #fff;
            text-align: center;
            padding: 15px 40px;
            font-size: 1rem;
            /* letter-spacing: 1.5px; */
            user-select: none;
            cursor: pointer;
            box-shadow: 5px 15px 25px rgba(0, 0, 0, 0.35);
            border-radius: 3px;
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
                            <input id="user_image" type="file" accept=".jpg, .jpeg, .png">
                            <label for="user_image"><i class="bi bi-upload"></i>Upload File</label>
                            <p>File size: maximum 1 MB<br>File extension: .JPEG, .PNG</p>
                        </div>
                        <div class="info-container">
                            <div class="input-container">
                                <div class="input-field">
                                    <h2>Name</h2>
                                    <input type="text" name="name" id="name" value='<?php echo htmlspecialchars($_SESSION['user_name']); ?>'>
                                </div>
                            </div>
                            <div class="input-container">
                                <div class="input-container">
                                    <h2>Email</h2>
                                    <input type="email" name="email" id="email" value='<?php echo htmlspecialchars($_SESSION['user_email']); ?>'>
                                </div>
                            </div>
                            <div class="input-container">
                                <div class="input-field">
                                    <h2>Phone</h2>
                                    <input type="text" name="phone" id="phone" value='<?php echo htmlspecialchars($_SESSION['user_phone']); ?>'>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Your Children</h1>
                        <p>Manage and protect your account</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>