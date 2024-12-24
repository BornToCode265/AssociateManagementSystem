<?php
require '../config.php';  // Your database connection file
header('Content-Type: application/json');  // Set content type for JSON response

$method = $_SERVER['REQUEST_METHOD'];

// Ensure the request method is POST for all actions
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Get the action parameter from the request to determine what to do
    $action = isset($data['action']) ? $data['action'] : null;

    switch ($action) {
        case 'authenticate':
            authenticateUser($pdo, $data);
            break;
        
        case 'getAllAssociates':
            getAllAssociates($pdo);
            break;

        case 'getAssociateById':
            if (isset($data['id'])) {
                getAssociateById($pdo, $data['id']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing associate ID']);
            }
            break;

        case 'updateAssociate':
            if (isset($data['id'])) {
                updateAssociate($pdo, $data);
            } else {
                echo json_encode(['success' => false, 'message' => 'Missing associate ID']);
            }
            break;

        default:
            http_response_code(400);  // Bad request
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
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
                    'username' => $admin['username'],
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

/**
 * Get all associates
 *
 * @param PDO $pdo
 */
function getAllAssociates($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `occupation`, `country`, `city`, `email`, `phone_number` FROM `associates`");
        $stmt->execute();
        $associates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $associates]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Get an associate by ID
 *
 * @param PDO $pdo
 * @param int $id
 */
function getAssociateById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT `id`, `name`, `occupation`, `country`, `city`, `email`, `phone_number` FROM `associates` WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $associate = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($associate) {
            echo json_encode(['success' => true, 'data' => $associate]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Associate not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

/**
 * Update an associate's details
 *
 * @param PDO $pdo
 * @param array $data
 */
function updateAssociate($pdo, $data) {
    if (isset($data['name'], $data['occupation'], $data['country'], $data['city'], $data['email'], $data['phone_number'])) {
        $id = $data['id'];
        $name = trim($data['name']);
        $occupation = trim($data['occupation']);
        $country = trim($data['country']);
        $city = trim($data['city']);
        $email = trim($data['email']);
        $phone_number = trim($data['phone_number']);

        try {
            // Query to update the associate details
            $stmt = $pdo->prepare("
                UPDATE `associates` 
                SET 
                    `name` = :name, 
                    `occupation` = :occupation, 
                    `country` = :country, 
                    `city` = :city, 
                    `email` = :email, 
                    `phone_number` = :phone_number
                WHERE `id` = :id
            ");
            $stmt->execute([
                'name' => $name,
                'occupation' => $occupation,
                'country' => $country,
                'city' => $city,
                'email' => $email,
                'phone_number' => $phone_number,
                'id' => $id,
            ]);

            if ($stmt->rowCount()) {
                echo json_encode(['success' => true, 'message' => 'Associate updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No changes made or associate not found']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }
}
?>
