<?php
include 'admin_header.html';

// Function to read users from the file and return an array of user details
function getUsersFromFile($filename)
{
    $users = [];
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $data = explode('|', $line);
            $users[] = [
                'name' => $data[0],
                'email' => $data[1],
                'username' => $data[2], // Change the order here to index 2
                'password' => $data[3], // Change the order here to index 3
                'profile_picture' => $data[4],
            ];
        }
    }
    return $users;
}

// Function to save users to the file
function saveUsersToFile($filename, $users)
{
    $data = '';
    foreach ($users as $user) {
        $data .= implode('|', [$user['name'], $user['email'], $user['username'], $user['password'], $user['profile_picture']]) . "\n";
    }
    file_put_contents($filename, $data);
}

// Call the function to get users from the file
$usersList = getUsersFromFile('users.txt');

// Handle search functionality
$searchQuery = $_GET['search'] ?? '';
$searchResults = [];
if ($searchQuery) {
    $searchResults = array_filter($usersList, function ($user) use ($searchQuery) {
        return stripos($user['name'], $searchQuery) !== false ||
               stripos($user['email'], $searchQuery) !== false ||
               stripos($user['username'], $searchQuery) !== false;
    });
} else {
    $searchResults = $usersList;
}

// Check if the form is submitted to add a new user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_user'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $profile_picture = $_FILES['profile_picture']['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate and sanitize the input data
    if (empty($name) || empty($email) || empty($profile_picture) || empty($username) || empty($password)) {
        echo 'Please fill in all fields.';
    } else {
        // Hash the password before saving
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Save user information to users.txt file
        $data = "$name|$email|$username|$hashedPassword|$profile_picture\n"; // Updated order
        file_put_contents('users.txt', $data, FILE_APPEND);

        // Move uploaded profile picture file to a directory
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], 'profile_pictures/' . $profile_picture);

        // Refresh the page to update the user list after adding a new user
        header('Location: view_users.php');
        exit;
    }
}

// Check if the request is to delete a user
if (isset($_GET['delete'])) {
    $deleteIndex = $_GET['delete'];
    if (isset($usersList[$deleteIndex])) {
        // Remove the user from the list
        unset($usersList[$deleteIndex]);
        // Re-index the array and save it to the file
        $usersList = array_values($usersList);
        saveUsersToFile('users.txt', $usersList);
        // Refresh the page to update the user list
        header('Location: view_users.php?search=' . urlencode($searchQuery));
        exit;
    }
}

// Check if the form is submitted to edit a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $editIndex = $_POST['edit_index'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $profile_picture = $_FILES['profile_picture']['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validate and sanitize the input data
    if (empty($name) || empty($email) || empty($username)) {
        echo 'Please fill in all fields.';
    } else {
        // Update user information
        $usersList[$editIndex] = [
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => $password ? password_hash($password, PASSWORD_DEFAULT) : $usersList[$editIndex]['password'], // Hash new password if provided
            'profile_picture' => $profile_picture ?: $usersList[$editIndex]['profile_picture'], // Use existing profile picture if not changed
        ];
        // Save updated users to the file
        saveUsersToFile('users.txt', $usersList);

        // Move uploaded profile picture file to a directory if a new profile picture is uploaded
        if ($profile_picture) {
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], 'profile_pictures/' . $profile_picture);
        }

        // Refresh the page to update the user list after editing
        header('Location: view_users.php?search=' . urlencode($searchQuery));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Users</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles7.css">
</head>
<body>
    <div class="content">
        <div class="user-container">
            <!-- Add New User Form Container -->
            <div class="user-item-upload-form">
                <h2>Add New User</h2>
                <form action="#" method="post" enctype="multipart/form-data">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <div class="file-drop-area" id="file-drop-area">
                        <span class="file-message">PROFILE PICTURE: Drag & Drop your file here or click to upload</span>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" required>
                    </div>

                    <button type="submit" name="upload_user">Upload User</button>
                </form>
            </div>

            <!-- Container for Added Users -->
            <div class="user-list">
                <!-- Search Form -->
                <form action="#" method="get">
                    <input type="text" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit">Search</button>
                </form>

                <!-- PHP loop to display added users -->
                <?php foreach ($searchResults as $index => $user) : ?>
                    <div class="user-item">
                        <img src="profile_pictures/<?php echo $user['profile_picture']; ?>" alt="<?php echo $user['name']; ?> Image">
                        <div class="user-info">
                            <p><strong>Name:</strong> <?php echo $user['name']; ?></p>
                            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
                            <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
                            <a href="?edit=<?php echo $index; ?>">Edit</a>
                            <a href="?delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                        </div>
                    </div>
                    <?php if (isset($_GET['edit']) && $_GET['edit'] == $index) : ?>
                        <div class="user-item edit-form">
                            <h2>Edit User</h2>
                            <form action="#" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="edit_index" value="<?php echo $index; ?>">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                                <label for="username">Username:</label>
                                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password">
                                <div class="file-drop-area" id="edit-file-drop-area">
                                    <span class="file-message">PROFILE PICTURE: Drag & Drop your file here or click to upload</span>
                                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                                </div>
                                <button type="submit" name="edit_user">Save Changes</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
<?php include 'footer.html'?>
</html>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const fileDropArea = document.getElementById('file-drop-area');
        const fileInput = fileDropArea.querySelector('input[type="file"]');
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
            fileInput.click();
        });

        fileInput.addEventListener('change', () => {
            fileMessage.textContent = fileInput.files[0].name;
        });
    });
</script>
