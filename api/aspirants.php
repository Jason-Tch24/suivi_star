<?php
require_once __DIR__ . '/../src/models/Database.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Aspirant.php';
require_once __DIR__ . '/../src/models/Ministry.php';

// Set JSON header
header('Content-Type: application/json');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $database = Database::getInstance();

    $userModel = new User($database);
    $aspirantModel = new Aspirant($database);
    $ministryModel = new Ministry($database);
    
    $user = $userModel->findById($_SESSION['user_id']);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            handleGet($aspirantModel, $ministryModel);
            break;
            
        case 'PUT':
            handleUpdate($aspirantModel, $user);
            break;
            
        case 'DELETE':
            handleDelete($aspirantModel, $user);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

function handleGet($aspirantModel, $ministryModel) {
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $aspirant = $aspirantModel->findByIdWithMinistry($id);
        
        if ($aspirant) {
            echo json_encode(['success' => true, 'aspirant' => $aspirant]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Aspirant not found']);
        }
    } else {
        // Return all aspirants
        $aspirants = $aspirantModel->getAllWithMinistries();
        echo json_encode(['success' => true, 'aspirants' => $aspirants]);
    }
}

function handleUpdate($aspirantModel, $user) {
    // Check permissions
    if (!in_array($user['role'], ['administrator', 'pastor'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        return;
    }
    
    $id = (int)$input['id'];
    $updateData = [
        'first_name' => $input['first_name'] ?? '',
        'last_name' => $input['last_name'] ?? '',
        'email' => $input['email'] ?? '',
        'phone' => $input['phone'] ?? null,
        'status' => $input['status'] ?? 'active',
        'current_step' => (int)($input['current_step'] ?? 1),
        'assigned_ministry_id' => !empty($input['assigned_ministry_id']) ? (int)$input['assigned_ministry_id'] : null,
        'notes' => $input['notes'] ?? null
    ];
    
    // Validate required fields
    if (empty($updateData['first_name']) || empty($updateData['last_name']) || empty($updateData['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'First name, last name, and email are required']);
        return;
    }
    
    // Validate email format
    if (!filter_var($updateData['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        return;
    }
    
    // Validate status
    $validStatuses = ['active', 'inactive', 'completed', 'suspended'];
    if (!in_array($updateData['status'], $validStatuses)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        return;
    }
    
    // Validate current step
    if ($updateData['current_step'] < 1 || $updateData['current_step'] > 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Current step must be between 1 and 6']);
        return;
    }
    
    // Get current aspirant to check for status changes
    $currentAspirant = $aspirantModel->findById($id);
    if (!$currentAspirant) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Aspirant not found']);
        return;
    }
    
    // Check if status is changing
    $statusChanged = $currentAspirant['status'] !== $updateData['status'];
    
    $success = $aspirantModel->updateComplete($id, $updateData);
    
    // If status changed and update was successful, send email notification
    if ($success && $statusChanged) {
        try {
            $aspirantModel->updateStatusWithEmail($id, $updateData['status']);
        } catch (Exception $e) {
            // Log but don't fail the update
            error_log('Failed to send status change email: ' . $e->getMessage());
        }
    }
    
    if ($success) {
        echo json_encode([
            'success' => true, 
            'message' => 'Aspirant updated successfully' . ($statusChanged ? ' (status change email sent)' : '')
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to update aspirant']);
    }
}

function handleDelete($aspirantModel, $user) {
    // Check permissions
    if (!in_array($user['role'], ['administrator', 'pastor'])) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient permissions']);
        return;
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        return;
    }
    
    $id = (int)$input['id'];
    
    // Check if aspirant exists
    $aspirant = $aspirantModel->findById($id);
    if (!$aspirant) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Aspirant not found']);
        return;
    }
    
    $success = $aspirantModel->delete($id);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Aspirant deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete aspirant']);
    }
}
?>
