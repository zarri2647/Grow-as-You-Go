<?php
// dashboard.php
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #f0f2f5; text-align: center; }
        .card { background: white; padding: 40px; display: inline-block; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        a { color: #0066cc; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="card">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>You have successfully logged in via SQLite.</p>
    <br>
    <a href="logout.php">Logout</a>
</div>

</body>
</html>
