<?php
session_start();

function getUserData($filePath) {
    $users = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($name, $email, $username, $passwordHash, $image) = explode('|', $line);
        $users[] = [
            'username' => $username,
            'email' => $email,
            'passwordHash' => $passwordHash
        ];
    }
    return $users;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $users = getUserData('users.txt');

    $loginSuccess = false;
    foreach ($users as $user) {
        if ($username === $user['username'] && password_verify($password, $user['passwordHash'])) {
            $_SESSION['username'] = $user['username'];
            $loginSuccess = true;
            break;
        }
    }

    if ($loginSuccess) {
        echo "Login successful! Welcome " . $_SESSION['username'];
    } else {
        echo "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles9.css">
</head>
<body>
<div class="container">
    <button class="back-button" onclick="goBack()">
        <svg xmlns="http://www.w3.org/2000/svg" height="40px" viewBox="0 -960 960 960" width="40px" fill="#FFFFFF"><path d="m287-446.67 240 240L480-160 160-480l320-320 47 46.67-240 240h513v66.66H287Z"/></svg>
    </button>
    <div class="desc">
        <h1>anytimeanywhere</h1>
        <p>Watch and borrow whenever you want,</p>
        <p>wherever you are.</p>
    </div>
    <div class="login-box">
        <h1>Login</h1>
        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
            <p><a href="#">Forgot Password?</a></p>
            <hr>
            <button type="button" class="create-account-btn">Create New Account</button>
        </form>
    </div>
</div>
<footer id="footer">
    <?php include "footer.html"?>
</footer>
</body>
</html>

<script>
    function goBack() {
        window.history.back();
    }
</script>
