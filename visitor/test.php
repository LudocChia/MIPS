<?php
// Database connection settings
$host = 'localhost';
$dbname = 'mahans';
$username = 'root';
$password = '';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to fetch all users
    $stmt = $pdo->query("SELECT * FROM visitor");
    $visitors = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="../visitor/test.css" >
    <link rel="icon" type="image/x-icon" href="../images/Mahans_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include "../visitor/header.php"; ?>
    <h1>Visitor History</h1>

    <div class="tHead">
        <p id="i1">No.</p>
        <p id="i2">Name</p>
        <p id="i3">Plate</p>
        <p id="i4">Time</p>
        <p id="i5">People</p>
        <p id="i6">Phone</p>
    </div>
    <?php foreach ($visitors as $visitor): ?>
    <div class="row">
        <p id="info1"><?= htmlspecialchars($visitor['No']) ?></p>
        <p id="info2"><?= htmlspecialchars($visitor['name']) ?></p>
        <p id="info3"><?= htmlspecialchars($visitor['plate_num']) ?></p>
        <p id="info4"><?= htmlspecialchars($visitor['time']) ?></p>
        <p id="info5"><?= htmlspecialchars($visitor['people']) ?></p>
        <p id="info6"><?= htmlspecialchars($visitor['phone_num']) ?></p>
    </div>
    <?php endforeach; ?>
</body>
</html>
