<?php
// docs/api/get_meeting.php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    // Fetch the most recent active meeting link
    $query = "SELECT * FROM meeting_links WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($meeting) {
        echo json_encode(['success' => true, 'meeting' => $meeting]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No active meeting links found.']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
