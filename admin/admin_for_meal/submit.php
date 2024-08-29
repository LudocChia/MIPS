<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mips";

try {
    // Establish a connection to the database using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set the PDO error mode to exception

    function generateID() {
        return uniqid(); // Function to generate a unique ID
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $id = generateID();
        $name = $_POST['Name'];
        $place = $_POST['Place'];
        $time = $_POST['Time'];
        $date = $_POST['Date'];
        $desc = $_POST['Desc'];
        $pic = $_POST['Pic'];
    
        // Prepare an SQL statement for execution
        $stmt = $conn->prepare("INSERT INTO `event` (`event_id`, `name`, `time`, `date`, `place`, `description`, `picture`) 
                                VALUES (:id, :name, :time, :date, :place, :description, :picture)");
    
        // Bind parameters to the SQL query
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':place', $place);
        $stmt->bindParam(':description', $desc);
        $stmt->bindParam(':picture', $pic);
    
        // Execute the prepared statement
        $stmt->execute();
    
        // Insert the first meal
        $mealID = generateID();
        $mealType = 1; // Corrected: Missing semicolon
    
        $stmt1 = $conn->prepare("INSERT INTO `event_meal` (`event_meal_id`, `event_id`, `meal_type_id`)
                                 VALUES (:mealID, :id, :mealType)");
    
        $stmt1->bindParam(':mealID', $mealID);
        $stmt1->bindParam(':id', $id);
        $stmt1->bindParam(':mealType', $mealType);
    
        $stmt1->execute();
        
        // Insert the second meal
        $mealID2 = generateID();
        $mealType2 = 2; // Corrected: Missing semicolon
    
        $stmt2 = $conn->prepare("INSERT INTO `event_meal` (`event_meal_id`, `event_id`, `meal_type_id`)
                                 VALUES (:mealID2, :id, :mealType2)");
    
        $stmt2->bindParam(':mealID2', $mealID2);
        $stmt2->bindParam(':id', $id);
        $stmt2->bindParam(':mealType2', $mealType2);
    
        $stmt2->execute();
    }
    

} catch(PDOException $e) {
    // Handle any PDO exceptions/errors
    echo "Connection failed: " . $e->getMessage();
}

// Close the PDO connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin_for_meal/donation.css">
</head>
<body style="background-image: url('tree.jpg'); ">
    <script src="../admin_for_meal/admin.js"></script>
    <?php include "../admin_for_meal/header.php";  ?>
    <dialog open class="modal" id="modal">
        <h1>Submitted Sucessfully !</h1>
        <a href="event.php"><input type="submit" value="Ok" id="okbtn"></a>
    </dialog>
</body>
</html>