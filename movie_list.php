<?php
include ("guest_header.html");
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

// Call the function to get movies from the file
$moviesList = getMoviesFromFile('movies.txt');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie List</title>
    <link rel="stylesheet" href="StylesheetsCSS/styles5.css">
</head>
<body>
    <div class="content">
        <div class="movie-container">
            <div class="video-container">
                 <video controls width="1130" height="650">
                <source src="image_front_page/video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="title-container">
                <h1>Breaking Bad</h1>
                <h2>2013 [16+] 5 Seasons</h2>
                <h4>Breaking Bad is an American crime drama television show created by Vince Gilligan.</h4>
                <h4>Set in Albuquerque, New Mexico, the series revolves around Walter White, a high-school</h4>
                <h4>chemistry teacher who turns to cooking and selling methamphetamine after being diagnosed</h4>
                <h4>with terminal lung cancer. Walter partners with his former student, Jesse Pinkman, to</h4>
                <h4>produce and distribute the drug. As they delve deeper into the criminal underworld, Walter’s</h4>
                <h4>transformation from a mild-mannered teacher to a ruthless drug kingpin unfolds. The show</h4>
                <h4>explores themes of morality, power, and the consequences of one’s choices. With its</h4>
                <h4>compelling characters, intense storytelling, and exceptional performances, Breaking Bad</h4>
                <h4>has left an indelible mark on television history.</h4>
                <h3>Ratings</h3>
                <p>⭐⭐⭐⭐⭐(4.8/5) stars</p>
            </div>
        </div>
        <div class="description">
            <h1>Newly Added Movies</h1>
        </div>
            <div class="movie-list">
                <?php foreach ($moviesList as $movie) : ?>
                    <div class="movie-item">
                        <img src="movie_images/<?php echo htmlspecialchars($movie['image']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?> Image">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="ratings">
    <h1>HIGHLY RECOMMENDED MOVIES</h1>
    <div class="recommended-images">
        <div class="movie-caption">
            <img src="rated_movies/image1.jpg" alt="Image 1 Description">
            <h3>Bianca Mekoum: ⭐⭐⭐⭐⭐</h3>
            <p>The most touching and best move in</p>
            <p>the world. You must watch and you</p>
            <p>will not regret it.</p>
        </div>
        <div class="movie-caption">
            <img src="rated_movies/image2.jpg" alt="Image 2 Description">
            <h3>James Tucker: ⭐⭐⭐⭐⭐</h3>
            <p>One of the best stories about coming</p>
            <p>up bull riders who are true champions</p>
            <p>weather they win that buckle or not.</p>
            <p>Some of those guys came up through</p>
            <p>the ranks and started the PBR as we</p>
            <p>know bull riding today. Lane Frost</p>
            <p>will be remembered as one of the</p>
            <p>very best.</p>
        </div>
        <div class="movie-caption">
            <img src="rated_movies/image3.jpg" alt="Image 3 Description">
            <h3>Sayeda Sajida: ⭐⭐⭐⭐⭐</h3>
            <p>Astonishing movie. One of the best rom</p>
            <p>-coms out there. It is so good that I</p>
            <p>could watch it on repeat for hours. So</p>
            <p>much better than the movies I was</p>
            <p>suggested by the web. I would definitely</p>
            <p>recommend watching this movie to fall</p>
            <p>in love with amazingness.</p>
        </div>
    </div>
</div>
</body>
<?php include 'footer.html'; ?>
</html>
