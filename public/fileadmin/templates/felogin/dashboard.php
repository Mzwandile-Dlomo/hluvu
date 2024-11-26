<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .welcome {
            margin-bottom: 20px;
        }
        .logout {
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="welcome">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    </div>
    
    <a href="logout.php" class="logout">Logout</a>
</body>
</html>