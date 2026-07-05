<?php
// database.php
try {
    // Creates or opens the SQLite database file
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL
    )");

    // Insert a dummy user for testing if the table is empty
    $stmt = $db->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
        $insertStmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $insertStmt->execute([
            ':username' => 'admin',
            ':password' => $hashedPassword
        ]);
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
