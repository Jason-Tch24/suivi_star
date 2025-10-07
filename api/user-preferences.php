<?php
/**
 * User Preferences API - STAR System
 * Handles user preference storage and retrieval
 */

session_start();

require_once __DIR__ . '/../src/middleware/Auth.php';
require_once __DIR__ . '/../src/models/Database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check authentication
if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$user = Auth::user();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetPreferences($user);
            break;
        case 'POST':
            handleSetPreference($user);
            break;
        case 'PUT':
            handleUpdatePreference($user);
            break;
        case 'DELETE':
            handleDeletePreference($user);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleGetPreferences($user)
{
    $db = Database::getInstance();
    
    $stmt = $db->prepare("SELECT preference_key, preference_value FROM user_preferences WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $preferences = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Decode JSON values
    $decodedPreferences = [];
    foreach ($preferences as $key => $value) {
        $decoded = json_decode($value, true);
        $decodedPreferences[$key] = $decoded !== null ? $decoded : $value;
    }
    
    echo json_encode($decodedPreferences);
}

function handleSetPreference($user)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $key = $input['key'] ?? '';
    $value = $input['value'] ?? '';
    
    if (empty($key)) {
        throw new Exception('Preference key is required');
    }
    
    $db = Database::getInstance();
    
    // Encode value as JSON if it's not a string
    $encodedValue = is_string($value) ? $value : json_encode($value);
    
    $stmt = $db->prepare("
        INSERT INTO user_preferences (user_id, preference_key, preference_value) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE preference_value = VALUES(preference_value)
    ");
    $stmt->execute([$user['id'], $key, $encodedValue]);
    
    echo json_encode(['success' => true, 'message' => 'Preference saved successfully']);
}

function handleUpdatePreference($user)
{
    handleSetPreference($user); // Same logic as set
}

function handleDeletePreference($user)
{
    $key = $_GET['key'] ?? '';
    
    if (empty($key)) {
        throw new Exception('Preference key is required');
    }
    
    $db = Database::getInstance();
    
    $stmt = $db->prepare("DELETE FROM user_preferences WHERE user_id = ? AND preference_key = ?");
    $stmt->execute([$user['id'], $key]);
    
    echo json_encode(['success' => true, 'message' => 'Preference deleted successfully']);
}
?>
