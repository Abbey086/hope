<?php
// docs/api/get_events.php
header('Content-Type: application/json');

require_once __DIR__ . '/db.php';

try {
    // Determine limit or highlighting from request parameters (optional)
    $is_highlighted = isset($_GET['highlighted']) ? intval($_GET['highlighted']) : null;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : null;

    $query = "SELECT * FROM events";
    
    // Add WHERE clause if fetching only highlighted
    if ($is_highlighted !== null) {
        $query .= " WHERE is_highlighted = " . $is_highlighted;
    }

    // Always sort by upcoming date first
    $query .= " ORDER BY event_date ASC";

    // Add LIMIT clause
    if ($limit !== null) {
        $query .= " LIMIT " . $limit;
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    // Fetch all events as associative array
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the events
    echo json_encode(['success' => true, 'events' => $events]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
