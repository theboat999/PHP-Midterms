<?php
include 'admin_header.html';

// Function to read movies from the file and return an array of movie details
function getMoviesFromFile($filename)
{
    $movies = [];
    if (file_exists($filename)) {
        $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $data = explode('|', $line);
            $movies[] = [
                'title' => $data[0],
                'genre' => $data[1],
                'year' => $data[2],
                'director' => $data[3],
                'image' => $data[4],
            ];
        }
    }
    return $movies;
}

// Function to save movies to the file
function saveMoviesToFile($filename, $movies)
{
    $data = '';
    foreach ($movies as $movie) {
        $data .= implode('|', $movie) . "\n";
    }
    file_put_contents($filename, $data);
}

// Call the function to get movies from the file
$moviesList = getMoviesFromFile('movies.txt');

// Handle search functionality
$searchQuery = $_GET['search'] ?? '';
$searchResults = [];
if ($searchQuery) {
    $searchResults = array_filter($moviesList, function ($movie) use ($searchQuery) {
        return stripos($movie['title'], $searchQuery) !== false ||
               stripos($movie['genre'], $searchQuery) !== false ||
               stripos($movie['year'], $searchQuery) !== false ||
               stripos($movie['director'], $searchQuery) !== false;
    });
}

// Check if the form is submitted to add a new movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_movie'])) {
    $title = $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $year = $_POST['year'] ?? '';
    $director = $_POST['director'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    // Validate and sanitize the input data
    if (empty($title) || empty($genre) || empty($year) || empty($director) || empty($image)) {
        echo 'Please fill in all fields.';
    } else {
        // Save movie information to movies.txt file
        $data = "$title|$genre|$year|$director|$image\n";
        file_put_contents('movies.txt', $data, FILE_APPEND);

        // Move uploaded image file to a directory
        move_uploaded_file($_FILES['image']['tmp_name'], 'movie_images/' . $image);

        // Refresh the page to update the movie list after adding a new movie
        header('Location: view_movies.php');
        exit;
    }
}

// Check if the request is to delete a movie
if (isset($_GET['delete'])) {
    $deleteIndex = $_GET['delete'];
    if (isset($moviesList[$deleteIndex])) {
        // Remove the movie from the list
        unset($moviesList[$deleteIndex]);
        // Re-index the array and save it to the file
        $moviesList = array_values($moviesList);
        saveMoviesToFile('movies.txt', $moviesList);
        // Refresh the page to update the movie list
        header('Location: view_movies.php?search=' . urlencode($searchQuery));
        exit;
    }
}

// Check if the form is submitted to edit a movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_movie'])) {
    $editIndex = $_POST['edit_index'];
    $title = $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $year = $_POST['year'] ?? '';
    $director = $_POST['director'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    // Validate and sanitize the input data
    if (empty($title) || empty($genre) || empty($year) || empty($director)) {
        echo 'Please fill in all fields.';
    } else {
        // Update movie information
        $moviesList[$editIndex] = [
            'title' => $title,
            'genre' => $genre,
            'year' => $year,
            'director' => $director,
            'image' => $image ?: $moviesList[$editIndex]['image'], // Use existing image if not changed
        ];
        // Save updated movies to the file
        saveMoviesToFile('movies.txt', $moviesList);

        // Move uploaded image file to a directory if a new image is uploaded
        if ($image) {
            move_uploaded_file($_FILES['image']['tmp_name'], 'movie_images/' . $image);
        }

        // Refresh the page to update the movie list after editing
        header('Location: view_movies.php?search=' . urlencode($searchQuery));
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Movies</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles4.css">
</head>
<body>
    <div class="content">
        <div class="movie-container">
            <div class="search-form">
                <form method="get" action="view_movies.php">
                    <input type="text" name="search" placeholder="Search for a movie" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <button type="submit" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' height=\'24px\' viewBox=\'0 -960 960 960\' width=\'24px\' fill=\'%23000000\'><path d=\'M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z\'/></svg>'); background-repeat: no-repeat; background-size: 24px 24px; padding-left: 28px;"></button>
                </form>
            </div>
            <div class="movie-list">
                <?php if ($searchQuery && empty($searchResults)) : ?>
                    <p>Your search did not match any movies.</p>
                <?php endif; ?>
                <?php foreach (($searchQuery ? $searchResults : $moviesList) as $index => $movie) : ?>
                    <div class="movie-item">
                        <img src="movie_images/<?php echo $movie['image']; ?>" alt="<?php echo $movie['title']; ?> Image">
                        <div class="movie-info">
                            <p><strong>Title:</strong> <?php echo $movie['title']; ?></p>
                            <p><strong>Genre:</strong> <?php echo $movie['genre']; ?></p>
                            <p><strong>Year:</strong> <?php echo $movie['year']; ?></p>
                            <p><strong>Director:</strong> <?php echo $movie['director']; ?></p>
                            <?php if ($searchQuery) : ?>
                                <a href="?search=<?php echo urlencode($searchQuery); ?>&edit=<?php echo $index; ?>">Edit</a>
                                <a href="?search=<?php echo urlencode($searchQuery); ?>&delete=<?php echo $index; ?>" onclick="return confirm('Are you sure you want to delete this movie?');">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (isset($_GET['edit']) && $_GET['edit'] == $index) : ?>
                        <div class="movie-item edit-form">
                            <h2>Edit Movie</h2>
                            <form action="#" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="edit_index" value="<?php echo $index; ?>">
                                <label for="title">Title:</label>
                                <input type="text" id="title" name="title" value="<?php echo $movie['title']; ?>" required>
                                <br>
                                <label for="genre">Genre:</label>
                                <input type="text" id="genre" name="genre" value="<?php echo $movie['genre']; ?>" required>
                                <br>
                                <label for="year">Year:</label>
                                <input type="number" id="year" name="year" value="<?php echo $movie['year']; ?>" required>
                                <br>
                                <label for="director">Director:</label>
                                <input type="text" id="director" name="director" value="<?php echo $movie['director']; ?>" required>
                                <br>
                                <label for="image">Movie Image:</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <br>
                                <button type="submit" name="edit_movie">Save Changes</button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="movie-item upload-form">
            <h2>Add New Movie</h2>
            <form action="#" method="post" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
                <br>
                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre" required>
                <br>
                <label for="year">Year:</label>
                <input type="number" id="year" name="year" required>
                <br>
                <label for="director">Director:</label>
                <input type="text" id="director" name="director" required>
                <br>
                <label for="image">Movie Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
                <br>
                <button type="submit" name="upload_movie">Upload Movie</button>
            </form>
        </div>
    </div>
    <?php include 'footer.html'; ?>
</body>
</html>