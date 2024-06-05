<?php
// Check if the form is submitted to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_user'])) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $profile_picture_name = $_FILES['profile_picture']['name'] ?? '';

    // Validate and sanitize the input data
    if (empty($first_name) || empty($last_name) || empty($email) || empty($username) || empty($password) || empty($profile_picture_name)) {
        echo 'Please fill in all fields including profile picture.';
    } else {
        // Combine first name and last name
        $full_name = "$first_name $last_name";

        // Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Move uploaded profile picture file to a directory
        $profile_picture_path = 'profile_pictures/' . $profile_picture_name;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path);

        // Prepare user data to be saved in the file
        $userData = "$full_name|$email|$username|$hashedPassword|$profile_picture_name\n";

        // Save user information to users.txt file
        file_put_contents('users.txt', $userData, FILE_APPEND);

        // Redirect to a success page or display a success message
        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles8.css">
</head>
<body>
    <div class="container">
        <div class="signup-box">
            <h1>Sign Up</h1>
            <p>It's quick and easy.</p>
            <div class="facebook">
                <button class="social-signup fb-signup">
                    <img src="icons/fb.webp" alt="Facebook Icon">
                    Login with Facebook
                </button>
            </div>
            <div class="google">
                <button class="social-signup google-signup">
                    <img src="icons/google.png" alt="Google Icon">
                    Login with Google
                </button>
            </div>
            <!-- Divider -->
            <div class="divider">
                <span>or</span>
            </div>
            <form action="#" method="post" enctype="multipart/form-data">
                <input type="text" name="first_name" placeholder="First name" required>
                <input type="text" name="last_name" placeholder="Last name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <!-- Add a file input for profile picture -->
                <div id="file-drop-area" class="file-drop-area">
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required> 
                    <span class="file-message">Drag & Drop your profile picture here</span>
                </div>
                <button type="submit" name="upload_user">Sign Up</button>
            </form>
        </div>
    </div>
    <footer>
        <?php include "footer.html"?>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fileDropArea = document.getElementById('file-drop-area');
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none'; // Hide the file input visually
            fileDropArea.appendChild(fileInput);

            const fileMessage = fileDropArea.querySelector('.file-message');

            fileDropArea.addEventListener('dragover', (event) => {
                event.preventDefault();
                fileDropArea.classList.add('dragging');
            });

            fileDropArea.addEventListener('dragleave', () => {
                fileDropArea.classList.remove('dragging');
            });

            fileDropArea.addEventListener('drop', (event) => {
                event.preventDefault();
                fileDropArea.classList.remove('dragging');
                fileInput.files = event.dataTransfer.files;
                fileMessage.textContent = fileInput.files[0].name;
            });

            fileDropArea.addEventListener('click', () => {
                fileInput.click(); // Trigger the file input when the area is clicked
            });

            fileInput.addEventListener('change', () => {
                fileMessage.textContent = fileInput.files[0].name;
            });
        });
    </script>
</body>
</html>
