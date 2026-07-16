<?php
// docs/api/db.php
$db_file = __DIR__ . '/data/database.sqlite';
$dir = dirname($db_file);

// Ensure the data directory exists
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

try {
    // Create (connect to) SQLite database in file
    $pdo = new PDO('sqlite:' . $db_file);
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize tables if they don't exist
    
    // Messages Table (Form submissions)
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        form_type TEXT NOT NULL,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        message TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        status TEXT DEFAULT 'unread',
        details TEXT -- JSON string for extra fields like phone, resume link, department etc.
    )");

    // Events Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS events (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        event_date DATE NOT NULL,
        location TEXT,
        image_url TEXT,
        is_highlighted INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Meeting Links Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS meeting_links (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        meeting_url TEXT NOT NULL,
        platform TEXT, -- e.g., 'Zoom', 'Google Meet'
        is_active INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

} catch (PDOException $e) {
    // Return JSON error if connection fails since this is an API
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}
?>
