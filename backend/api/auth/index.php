<?php
require '../config.php';  // Your database connection file
header('Content-Type: application/json');  // Set content type for JSON response

$method = $_SERVER['REQUEST_METHOD'];

// Check if the request method is POST (for login)
if ($method === 'POST') {
    authenticateUser($pdo, json_decode(file_get_contents('php://input'), true));
} else {
    http_response_code(405);  // Method not allowed
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}

/**
 * Handle user authentication (login)
 *
 * @param PDO $pdo
 * @param array $data
 */
function authenticateUser($pdo, $data) {
    if (isset($data['email']) && isset($data['password'])) {
        $email = trim($data['email']);
        $password = trim($data['password']);

        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Generate token or other session-related information
                $token = bin2hex(random_bytes(16));  // Example token (use JWT or sessions in real cases)

                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'token' => $token,
                    'username'=> $admin['username'],
                      
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing email or password']);
    }
}
