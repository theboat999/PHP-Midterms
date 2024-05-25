<?php

session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Setting username and password for admin login
    if ($username === 'admin' && $password === 'admin123') {
        header('Location: view_movies.php');
    } else {
        $error = 'Incorrect username or password';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles2.css">
</head>
<body>
    <button class="back-button" onclick="goBack()">
    <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="m287-446.67 240 240L480-160 160-480l320-320 47 46.67-240 240h513v66.66H287Z"/></svg>
    </button>
    <div class="admin-container">           
        <h2>Admin Panel</h2>
        <form action="admin.php" method="post">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <br>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <br>
            <button type="submit">Login</button>
        </form>

        <?php
        if ($error) {
            echo "<p class='error'>$error</p>";
        } 
        ?>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
    
</body>
</html>
