<?php
session_start();
require '../private/autoload.php';

// Check if the user is logged in
$user_data = check_login($con);
if (!$user_data) {
    header("Location: login.html");
    exit;
}

$user_id = $user_data['user_id'];

// Fetch lectures from the database
$query = "SELECT * FROM lectures WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lectures = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
date_default_timezone_set('Europe/London');

// Capitalize the first character of each lecture's day
foreach ($lectures as &$lecture) {
    $lecture['day'] = ucfirst($lecture['day']);
}

$lecturesJson = json_encode($lectures);

//$currentDay = date('w'); // Get current day of the week as an integer (0 for Sunday, 1 for Monday, etc.)
//$currentTime = date('H:i'); // Get current time in 'HH:MM' format

//$lectures = array_filter($lectures, function ($lecture) use ($currentDay, $currentTime) {
//    $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
//    $lectureDay = array_search(strtolower($lecture['day']), $days); // Ensure case-insensitive comparison
//    $lectureStartTime = strtotime($lecture['start_time']);
//    $lectureEndTime = strtotime($lecture['end_time']);
//    $currentTime = strtotime($currentTime);

    // Debugging output
//    error_log("Lecture Day: $lectureDay, Current Day: $currentDay, Lecture Start Time: $lectureStartTime, Lecture End Time: $lectureEndTime, Current Time: $currentTime");

    // Check if the lecture is today and currently running or if it is on a future day
//    return (($lectureDay === $currentDay) && $currentTime >= $lectureStartTime && $currentTime <= $lectureEndTime) || ($lectureDay > $currentDay);
//});

//$lectures = array_values($lectures);
// Debugging output for filtered lectures
//$lecturesJson = json_encode($lectures);
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
            <a href="add_lecture.php">Add Lecture</a>
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

        document.addEventListener('DOMContentLoaded', function() {
            const lectureContainer = document.getElementById('lecture-container');

            // Create placeholder item
            const placeholderItem = document.createElement('div');
            placeholderItem.className = 'item';
            placeholderItem.style.backgroundImage = 'url(image/default.jpg)';
            placeholderItem.innerHTML = `
                <div class="content">
                    <div class="name"></div>
                    <div class="des"></div>
                    <a class="onedrive-btn" href="#" target="_blank">
                        <img src="image/onedrive_icon.png" alt="OneDrive Icon"> OneDrive
                    </a>
                    <a class="moodle-btn" href="#" target="_blank">
                        <img src="image/moodle_icon.png" alt="Moodle Icon"> Moodle
                    </a>
                    <a class="presto-btn" href="#" target="_blank">
                        <img src="image/presto_icon.png" alt="Presto Icon"> Presto
                    </a>
                    <a class="map-btn" href="#" target="_blank">
                        <img src="image/maps_icon.png" alt="Map Icon"> Map
                    </a>
                </div>
            `;
            lectureContainer.appendChild(placeholderItem);

            // Create items for each lecture
            lectures.forEach(lecture => {
                const item = document.createElement('div');
                item.className = 'item';
                item.style.backgroundImage = 'url(image/default.jpg)';

                const content = document.createElement('div');
                content.className = 'content';

                const name = document.createElement('div');
                name.className = 'name';
                name.textContent = lecture.module_name;

                const des = document.createElement('div');
                des.className = 'des';
                des.innerHTML = `
                    <p><strong>${lecture.module_code}</strong></p>
                    <p>${lecture.day}</p>
                    <p>${lecture.start_time} - ${lecture.end_time}</p>
                    <p>${lecture.location}</p>
                `;

                const onedriveBtn = document.createElement('a');
                onedriveBtn.className = 'onedrive-btn';
                onedriveBtn.href = lecture.onedrive_link || '#';
                onedriveBtn.target = '_blank';
                onedriveBtn.innerHTML = '<img src="image/onedrive_icon.png" alt="OneDrive Icon"> OneDrive';

                const moodleBtn = document.createElement('a');
                moodleBtn.className = 'moodle-btn';
                moodleBtn.href = lecture.moodle_link || '#';
                moodleBtn.target = '_blank';
                moodleBtn.innerHTML = '<img src="image/moodle_icon.png" alt="Moodle Icon"> Moodle';

                const prestoBtn = document.createElement('a');
                prestoBtn.className = 'presto-btn';
                prestoBtn.href = lecture.presto_link || '#';
                prestoBtn.target = '_blank';
                prestoBtn.innerHTML = '<img src="image/presto_icon.png" alt="Presto Icon"> Presto';

                const mapBtn = document.createElement('a');
                mapBtn.className = 'map-btn';
                mapBtn.href = lecture.maps_link || '#';
                mapBtn.target = '_blank';
                mapBtn.innerHTML = '<img src="image/maps_icon.png" alt="Map Icon"> Map';

                content.appendChild(name);
                content.appendChild(des);
                content.appendChild(onedriveBtn);
                content.appendChild(moodleBtn);
                content.appendChild(prestoBtn);
                content.appendChild(mapBtn);

                item.appendChild(content);
                lectureContainer.appendChild(item);
            });
        });
        </script>

        <script>
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