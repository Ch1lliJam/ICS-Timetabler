<?php
session_start();
require '../private/autoload.php';

// Check if the user is logged in
$user_data = check_login($con);
if (!$user_data) {
    header("Location: loginpage.php");
    exit;
}
$user_id = $user_data['user_id'];
$limit = 20;

// remove old lectures
removeOldLectures($user_id, $con);

$query = "SELECT * FROM lectures WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lectures = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch lectures from the database
$lecturesJson = json_encode($lectures);
date_default_timezone_set('Europe/London');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChilliJam | My Lectures</title>
    <link rel="stylesheet" href="timetable.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="WIP-page">
    <!-- Navbar -->
    <nav>
        <input type="checkbox" id="sidebar-active">
        <label for="sidebar-active" class="open-sidebar-button">
            <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32"><path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/></svg>
        </label>
        <label id="overlay" for="sidebar-active"></label>
        <div class="links-container">
            <label for="sidebar-active" class="close-sidebar-button">
                <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z"/></svg>
            </label>

            <a class="home-link" href="view_lectures.php">ChilliJam</a>
            <a class="active" href="view_lectures.php">Lectures</a>
            <a href="modify_lectures.php">Edit Lectures</a>
            <a href="logout.php">Log out</a>
        </div>
    </nav>

    <!-- Date and University Week -->
    <section class="date-section">
        <h1 id="current-date"></h1>
        <h2 id="current-week"></h2>
    </section>

    <!-- Lectures Slide -->
    <div class="container">
        <div class="slide" id="lecture-container">
            <!-- Items will be dynamically generated here -->
        </div>


        <script>
            const lectures = <?= $lecturesJson; ?>;
            console.log("Lectures:", lectures);
        </script>
        <script src="app.js"></script>

        <!-- Navigation Buttons -->
        <div class="button">
            <button class="prev"><i class="fa-solid fa-arrow-left"></i></button>
            <button class="next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>

    <!-- Attribution -->
    <div class="attribution">
        <a href="">(images used for personal use only)</a>
    </div>

    <script>
        // Carousel navigation
        const next = document.querySelector('.next');
        const prev = document.querySelector('.prev');

        next.addEventListener('click', () => {
            const items = document.querySelectorAll('.item');
            document.querySelector('.slide').appendChild(items[0]);
        });

        prev.addEventListener('click', () => {
            const items = document.querySelectorAll('.item');
            document.querySelector('.slide').prepend(items[items.length - 1]);
        });

        // Date and University Week
        document.addEventListener('DOMContentLoaded', () => {
            const dateElement = document.getElementById('current-date');
            const weekElement = document.getElementById('current-week');
            const today = new Date();
            const formattedDate = today.toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });
            dateElement.textContent = formattedDate;

            const startDate = new Date('2024-08-05'); // University start date
            const oneWeek = 7 * 24 * 60 * 60 * 1000; // Milliseconds in a week
            const currentWeek = Math.floor((today - startDate) / oneWeek) + 1;
            weekElement.textContent = "University Week: " + currentWeek;
            const prevButton = document.querySelector('.prev');
            prevButton.click();
        });

    </script>
</body>
</html>