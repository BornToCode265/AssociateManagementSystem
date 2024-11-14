<?php
session_start();
require '../config.php';

// Get username and password from POST request
$username = $_POST['username'];
$password = $_POST['password'];

try {
    // Fetch the admin user from the database
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    // Verify the password
    if ($admin && password_verify($password, $admin['password'])) {
        // Set session variable
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];

        // Redirect to the admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Invalid credentials
        echo "Invalid username or password.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
