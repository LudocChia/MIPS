<?php
$db_name = "mysql:host=localhost;dbname=mahans";
$db_username = "root";
$db_password = "";

try {
    $pdo = new PDO($db_name, $db_username, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// function unique_id()
// {
//     $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
//     $charLength = strlen($chars);
//     $randomString = "";
//     for ($i = 0; $i < 15; $i++) {
//         $randomString .= $chars[mt_rand(0, $charLength - 1)];
//     }
//     return $randomString;
// }
