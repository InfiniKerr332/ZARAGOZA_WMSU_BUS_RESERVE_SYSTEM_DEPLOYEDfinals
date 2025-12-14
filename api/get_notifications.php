<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

// Load notification functions
if (!function_exists('get_unread_notifications')) {
    require_once __DIR__ . '/../includes/notifications.php';
}

header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'count' => 0,
        'notifications' => []
    ]);
    exit;
}

try {
    $user_id = (int)$_SESSION['user_id'];
    
    // Auto-mark as read when fetched if requested
    if (isset($_GET['mark_read']) && $_GET['mark_read'] == '1') {
        mark_all_notifications_read($user_id);
    }

    $notifications = get_unread_notifications($user_id);
    $count = get_unread_count($user_id);

    echo json_encode([
        'success' => true,
        'count' => $count,
        'notifications' => $notifications
    ]);
} catch (Exception $e) {
    error_log("Notification API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'count' => 0,
        'notifications' => [],
        'error' => 'Failed to load notifications'
    ]);
}