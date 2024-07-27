<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahans School Bookshop</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_internation_primary_school_logo.png">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <?php include "./components/header.php"; ?>
    <section class="products">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans International Primary School Bookshop</h1>
                    </div>
                    <div class="right">
                        <p>Found <b id="count">0</b> results</p>
                    </div>
                </div>
                <div class="box-container" id="list">
                    <!-- Product boxes will be here -->
                </div>
            </div>
        </div>
    </section>
    <a href="#" class="back-to-top">
        <span class="material-symbols-outlined">arrow_upward</span>
    </a>
    <?php include './components/footer.php'; ?>
    <script src="./js/common.js"></script>
</body>

</html>