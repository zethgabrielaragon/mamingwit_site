<?php
// ============================================================
// MAMINGWIT CHECKER - API Handler
// ============================================================

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/analyzer.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'analyze':
        $url = $_POST['url'] ?? '';
        
        if (empty($url)) {
            echo json_encode(['error' => true, 'message' => 'No URL provided.']);
            break;
        }

        // Basic URL sanitization
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);

        // Add protocol if missing
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'http://' . $url;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('/^https?:\/\/\d{1,3}(\.\d{1,3}){3}/', $url)) {
            echo json_encode(['error' => true, 'message' => 'Invalid URL format. Please enter a valid URL.']);
            break;
        }

        $analyzer = new URLAnalyzer($url);
        $result = $analyzer->analyze();
        
        // Add community reports count
        $result['community_reports'] = $result['community_reports'] ?? 0;

        echo json_encode(['error' => false, 'data' => $result]);
        break;

    case 'report':
        $url = $_POST['url'] ?? '';
        $reason = $_POST['reason'] ?? 'Reported as suspicious';

        if (empty($url)) {
            echo json_encode(['error' => true, 'message' => 'No URL provided.']);
            break;
        }

        $result = submitCommunityReport($url, $reason);
        echo json_encode($result);
        break;

    case 'history':
        $history = getURLHistory(25);
        echo json_encode(['error' => false, 'data' => $history]);
        break;

    case 'stats':
        $stats = getDashboardStats();
        echo json_encode(['error' => false, 'data' => $stats]);
        break;

    default:
        echo json_encode(['error' => true, 'message' => 'Invalid action.']);
        break;
}
?>