<?php
session_start();
include "../components/db_connect.php";

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

// Check if event_id is provided
$event_id = isset($_GET['event_id']) ? htmlspecialchars($_GET['event_id']) : null;

if ($event_id) {
    try {
        // Prepare and execute SQL query with placeholders
        $stmt = $pdo->prepare("SELECT * FROM `event` WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $row = $stmt->fetch();

        // Display data
        if ($row) {
            // echo '<p>Event_ID: ' . htmlspecialchars($row['event_id']) . '</p>';
            // echo '<p>Name: ' . htmlspecialchars($row['name']) . '</p>';
        } else {
            echo 'No data found';
        }
    } catch (PDOException $e) {
        // Handle potential errors
        echo 'Database error: ' . htmlspecialchars($e->getMessage());
    }
} else {
    echo "No event selected.";
}
try {
    // Prepare the SQL statement with placeholders
    $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                            INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                            INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                            WHERE event_meal.event_id = :event_id ");

    // Bind the event_id parameter
    $stmt->bindParam(':event_id', $event_id);

    // Execute the prepared statement
    $stmt->execute();

    // Fetch all results
    $mealrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($mealrows) {
        //In order to use this code , you must change fetchAll(PDO::FETCH_ASSOC) to fetch()
        // echo '<p>Event_ID: ' . htmlspecialchars($mealrows['event_id']) . '</p>';
        // echo '<p>Meal_Type_ID: ' . htmlspecialchars($mealrows['meal_type_id']) . '</p>';
    } else {
        // echo 'No data found';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
// echo '<pre>';
// var_dump($mealrows);
// echo '</pre>';
// 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../admin/admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin/admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin/admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin/admin_meal/adminDonation.css">
</head>

<body>
    <?php include "customer_header.php";  ?>
    <script src="../admin/admin_for_meal/admin.js"></script>
    <div class="bigbox">
        <row id="row1">
            <a href="donationMain.php"><i class='bx bx-arrow-back'></i></a>
        </row>
        <pic>
            <div class="slider">
                <div class="slides">
                    <!-- Slide 1 -->
                    <div class="slide"><img src="../admin/admin_for_meal/pngwing.com.png" alt="Image 1"></div>
                    <!-- Add more slides as needed -->
                </div>
                <!-- Left and right controls -->
                <!-- <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a> -->
            </div>
        </pic>
        <row id="row2">
            <row1>
                <?php if ($row): ?>
                    <h1><?= htmlspecialchars($row['name']) ?></h1>
                <?php else: ?>
                    <p>No data found</p>
                <?php endif; ?>
            </row1>
            <!-- <row2>
                <button id="edit">
                    <i class='bx bx-edit'>edit</i>
                </button>
            </row2> -->
        </row>
        <row id="row3">
            <i class='bx bx-time-five'></i>
            <p><?= htmlspecialchars($row['time']) ?></p>
        </row>
        <row id="row4">
            <i class='bx bx-calendar'></i>
            <p><?= htmlspecialchars($row['date']) ?></p>
        </row>
        <row id="row4">
            <i class='bx bx-current-location'></i>
            <p><?= htmlspecialchars($row['place']) ?></p>
        </row>
        <row id="row5">
            <p id="desc"><?= htmlspecialchars($row['description']) ?></p>
        </row>
        <column1>
            <h2>Food and beverage</h2>
            <p>Meal provided:</p>
            <row>
                <?php
                // Base URL for the next page with the event_id parameter
                $baseUrl = 'allMeal.php?' . http_build_query([
                    'event_id' => $row['event_id']
                ]);

                // URLs for each button, including both event_id and meal_type_id
                $breakfastUrl = $baseUrl . '&' . http_build_query(['meal_type_id' => 1]);
                $lunchUrl = $baseUrl . '&' . http_build_query(['meal_type_id' => 2]);

                // Debug: Output the URLs for verification
                // Uncomment the lines below to see the URLs being generated
                // echo '<p>Breakfast URL: ' . htmlspecialchars($breakfastUrl) . '</p>';
                // echo '<p>Lunch URL: ' . htmlspecialchars($lunchUrl) . '</p>';
                ?>
                <a href="<?= htmlspecialchars($breakfastUrl) ?>" style="text-decoration: none; color: inherit;">
                    <p id="first">Breakfast</p>
                </a>
                <a href="<?= htmlspecialchars($lunchUrl) ?>" style="text-decoration: none; color: inherit;">
                    <p id="second">Lunch</p>
                </a>
            </row>
        </column1>
        <row id="row6">
            <h2>Food provided for this event :</h2>
        </row>
        <div class="slider-container1">
            <div class="slides1">
                <?php if (!empty($mealrows)): ?>
                    <?php foreach ($mealrows as $mealrow): ?>
                        <div class="slide1">
                            <h3><?= htmlspecialchars($mealrow['meal_name']) ?></h3>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p id="nRecord">No records found.</p>
                <?php endif; ?>
            </div>
            <!-- Slider Controls -->
            <div class="slider-controls1">
                <p class="prev1" onclick="slider1.plusSlides(-1)">&#10094;</p>
                <p class="next1" onclick="slider1.plusSlides(1)">&#10095;</p>
            </div>
        </div>

        <!-- <button class="delete">
            <i class='bx bx-comment-x'></i>
            <p>Delete Event</p>
        </button> -->

    </div>
    <script>
        // let slideIndex = 0;

        // function showSlides(n) {
        //     const slides = document.querySelectorAll('.slide');
        //     const slidesContainer = document.querySelector('.slides');
        //     const totalSlides = slides.length;

        //     if (n >= totalSlides) {
        //         slideIndex = 0;
        //     } else if (n < 0) {
        //         slideIndex = totalSlides - 1;
        //     } else {
        //         slideIndex = n;
        //     }

        //     // Apply sliding effect by translating the slides container
        //     slidesContainer.style.transform = `translateX(-${slideIndex * 100}%)`;
        // }

        // function plusSlides(n) {
        //     showSlides(slideIndex + n);
        // }

        // // Initialize the slider
        // showSlides(slideIndex);

        (function() {
            function Slider(containerSelector, slidesSelector, controlsSelector, slidesToShow) {
                this.container = document.querySelector(containerSelector);
                this.slides = document.querySelectorAll(slidesSelector);
                this.controls = document.querySelector(controlsSelector);
                this.slideIndex = 0;
                this.slidesToShow = slidesToShow || 3;

                this.init();
            }

            Slider.prototype.init = function() {
                const self = this;
                this.updateSlideWidth();
                window.addEventListener('resize', function() {
                    self.updateSlideWidth();
                    self.showSlides(self.slideIndex);
                });
                this.showSlides(this.slideIndex);
            };

            Slider.prototype.updateSlideWidth = function() {
                const slideWidth = (this.container.clientWidth - (this.slidesToShow - 1)) / this.slidesToShow;
                this.slides.forEach(slide => {
                    slide.style.width = `${slideWidth}px`;
                });
            };

            Slider.prototype.showSlides = function(index) {
                const totalSlides = this.slides.length;

                // Ensure slideIndex is within range
                if (index >= totalSlides - this.slidesToShow + 1) {
                    this.slideIndex = 0;
                } else if (index < 0) {
                    this.slideIndex = totalSlides - this.slidesToShow;
                } else {
                    this.slideIndex = index;
                }

                const slideWidth = (this.container.clientWidth - (this.slidesToShow - 1)) / this.slidesToShow;
                this.container.querySelector('.slides1').style.transform = `translateX(-${this.slideIndex * (slideWidth + 1)}px)`;
            };

            Slider.prototype.plusSlides = function(n) {
                this.showSlides(this.slideIndex + n);
            };

            // Initialize the specific slider instance
            window.slider1 = new Slider('.slider-container1', '.slide1', '.slider-controls1', 3);
        })();
    </script>
</body>

</html>