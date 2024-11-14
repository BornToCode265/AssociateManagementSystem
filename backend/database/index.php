<?php
// create_associates_and_admins.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection parameters
    $host = "localhost";  // Change to your host
    $dbname = "associates_db";  // Database name to create
    $username = "root";  // Change to your MySQL username
    $password = "";  // Change to your MySQL password

    // This output buffer helps send feedback in real-time
    ob_start();

    echo "<p>Starting the database setup process...</p>";

    try {
        // Create a new PDO connection
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Begin transaction
        $pdo->beginTransaction();
        echo "<p>Connection to the database server successful.</p>";

        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
        $pdo->exec("USE $dbname");
        echo "<p>Database '$dbname' created or already exists.</p>";

        // Create associates table
        $createAssociatesTable = "
            CREATE TABLE IF NOT EXISTS associates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                occupation VARCHAR(255),
                country VARCHAR(100),
                city VARCHAR(100),
                email VARCHAR(255),
                phone_number VARCHAR(15)
            );
        ";
        $pdo->exec($createAssociatesTable);
        echo "<p>'associates' table created successfully.</p>";

        // Create admins table
        $createAdminsTable = "
            CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(255) NOT NULL
            );
        ";
        $pdo->exec($createAdminsTable);
        echo "<p>'admins' table created successfully.</p>";

        // Insert fake data into associates table
        $insertAssociates = "
            INSERT INTO associates (name, occupation, country, city, email, phone_number)
            VALUES
                ('Chimwemwe Banda', 'Engineer', 'Malawi', 'Lilongwe', 'chimwemwe.banda@gmail.com', '265998123456'),
                ('Thoko Chirwa', 'Doctor', 'Malawi', 'Blantyre', 'thoko.chirwa@gmail.com', '265991234567'),
                ('Mwiza Phiri', 'Teacher', 'Malawi', 'Mzuzu', 'mwiza.phiri@gmail.com', '265997654321'),
                ('Tamanda Moyo', 'Nurse', 'Malawi', 'Zomba', 'tamanda.moyo@gmail.com', '265995432109');
        ";
        $pdo->exec($insertAssociates);
        echo "<p>Fake data inserted into 'associates' table.</p>";

        // Insert fake data into admins table (with hashed passwords)
        $insertAdmins = "
            INSERT INTO admins (username, password)
            VALUES
                ('admin1', '" . password_hash('password123', PASSWORD_DEFAULT) . "'),
                ('admin2', '" . password_hash('password456', PASSWORD_DEFAULT) . "');
        ";
        $pdo->exec($insertAdmins);
        echo "<p>Fake data inserted into 'admins' table.</p>";

        // Commit transaction
        $pdo->commit();
        echo "<p>Database setup completed successfully!</p>";

        echo "<p>Click the button below to close this setup process.</p>";
        echo "<button onclick='window.close()'>Close</button>";

    } catch (PDOException $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        echo "<p>Setup failed. Please check the error and try again.</p>";
    }

    // Send the buffer content to the browser
    ob_flush();
    flush();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Database Setup for Associates and Admins</h1>
        <p class="text-center">Click the button below to initiate the database setup process.</p>
        
        <div class="text-center">
            <form action="" method="POST">
                <button type="submit" class="btn btn-primary">Start Database Setup</button>
            </form>
        </div>

        <div id="setup-output" class="mt-4 text-center">
            <!-- The output from PHP will be displayed here -->
        </div>
    </div>
</body>
</html>
