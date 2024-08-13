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
    $search = mysqli_query($conn,$sql);
    

    // Execute the query and check if the insertion was successful
    if ($conn->query($search) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>