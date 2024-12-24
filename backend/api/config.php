<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: *");


// Database configuration
$host = "localhost";  // Change to your host
$dbname = "associates_db";  // Database name to create
$username = "root";  // Change to your MySQL username
$password = "";
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Set the PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optionally, set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Optional: Display a connection success message for debugging
    // echo "Database connected successfully!";
} catch (PDOException $e) {
    // Display error message if the connection fails
    die("Database connection failed: " . $e->getMessage());
}
