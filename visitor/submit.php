<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mahans";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $name = $conn->real_escape_string($_POST['Name']);
    $phone = $conn->real_escape_string($_POST['Phone']);
    $email = $conn->real_escape_string($_POST['Email']);
    $company = $conn->real_escape_string($_POST['Company']);
    $plate = $conn->real_escape_string($_POST['Plate']);
    $date = $conn->real_escape_string($_POST['Date']);
    $time = $conn->real_escape_string($_POST['Time']);
    $people = $conn->real_escape_string($_POST['People']);
    $purpose = $conn->real_escape_string($_POST['Purpose']);

    // SQL query to insert data into the database
    $sql = "INSERT INTO `visitor`(`name`, `phone_num`, `email`, `company`, `purpose`, `plate_num`, `time`, `date`, `people`) 
    VALUES ('$name','$phone','$email','$company','$purpose','$plate','$time','$date','$people')";
    
    

    // Execute the query and check if the insertion was successful
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submitted successfully</title>
    <link rel="stylesheet" href="../visitor/visitor.css" >
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body style="background-image: url('tree.jpg')">
    <?php include "../visitor/header.php"; ?>
    <dialog open class="modal" id="modal">
        <h1>Submitted Sucessfully !</h1>
        <input type="submit" value="Ok" id="btn1">
    </dialog>
</body>
</html>