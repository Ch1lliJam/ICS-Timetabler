<?php
session_start();
require '../private/autoload.php'; 

// Check if user is logged in
$user_data = check_login($con);
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

// Handle form submission for modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modify'])) {
    $lecture_id = $_POST['lecture_id'];
    $module_code = trim($_POST['module_code']);
    $module_name = trim($_POST['module_name']);
    $day = trim(strtolower($_POST['day']));
    $location = trim($_POST['location']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $onedrive_link = !empty($_POST['onedrive_link']) ? trim($_POST['onedrive_link']) : null;
    $moodle_link = !empty($_POST['moodle_link']) ? trim($_POST['moodle_link']) : null;
    $presto_link = !empty($_POST['presto_link']) ? trim($_POST['presto_link']) : null;
    $maps_link = !empty($_POST['maps_link']) ? trim($_POST['maps_link']) : null;

    // Update the lecture in the database
    $query = "UPDATE lectures SET module_code = ?, module_name = ?, day = ?, location = ?, start_time = ?, end_time = ?, onedrive_link = ?, moodle_link = ?, presto_link = ?, maps_link = ? WHERE lecture_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param(
        "ssssssssssi",
        $module_code,
        $module_name,
        $day,
        $location,
        $start_time,
        $end_time,
        $onedrive_link,
        $moodle_link,
        $presto_link,
        $maps_link,
        $lecture_id
    );

    if ($stmt->execute()) {
        echo "<p style='color:green; margin-top: 100px; margin-left: 20px; font-size: 24px;'>Lecture modified successfully!";
    } else {
        echo "<p style='color:red; margin-top: 100px; margin-left: 20px; font-size: 24px;'>Error: " . $stmt->error;
    }
}

// Handle deletion of a lecture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $lecture_id = $_POST['lecture_id'];

    // Delete the lecture from the database
    $query = "DELETE FROM lectures WHERE lecture_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $lecture_id);

    if ($stmt->execute()) {
        echo "<p style='color:green; margin-top: 100px; margin-left: 20px; font-size: 24px;'>Lecture deleted successfully!";
    } else {
        echo "<p style='color:red; margin-top: 100px; margin-left: 20px; font-size: 24px;'>Error: " . $stmt->error;
    }
}

// Retrieve all lectures associated with the user_id
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM lectures WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$lectures = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Function to get the index of the day
function getDayIndex($day) {
    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    return array_search(strtolower($day), $days);
}

// Sort the lectures by day and then by start time
usort($lectures, function($a, $b) {
    $dayA = getDayIndex($a['day']);
    $dayB = getDayIndex($b['day']);

    if ($dayA !== $dayB) {
        return $dayA - $dayB; // Sort by day index
    }

    // If the days are the same, sort by start time
    $startTimeA = strtotime($a['start_time']);
    $startTimeB = strtotime($b['start_time']);
    return $startTimeA - $startTimeB;
});

// Capitalize the first character of each lecture's day
foreach ($lectures as &$lecture) {
    $lecture['day'] = ucfirst($lecture['day']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ch1lliJam | Modify Lectures</title>
    <link rel="stylesheet" href="timetable.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="lecture-page">
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
            <a href="view_lectures.php">Lectures</a>
            <a href="modify_lectures.php" class="active">Edit Lectures</a>
            <a href="settingspage.php">Settings</a>
            <a href="logout.php">Log out</a>
        </div>
    </nav>


    <!-- Modify Lecture Form -->
    <div id="modify-form" style="display:none;">
        <h2>Modify Lecture</h2>
        <form method="post">
            <input type="hidden" name="lecture_id" id="modify-lecture-id">
            <div class="inputbox">
                <input type="text" name="module_code" id="modify-module-code" required placeholder=" ">
                <label>Module Code</label>
            </div>
            <div class="inputbox">
                <input type="text" name="module_name" id="modify-module-name" required placeholder=" ">
                <label>Module Name</label>
            </div>
            <div class="inputbox">
                <input type="text" name="day" id="modify-day" required placeholder=" ">
                <label>Day</label>
            </div>
            <div class="inputbox">
                <input type="text" name="location" id="modify-location" required placeholder=" ">
                <label>Location</label>
            </div>
            <div class="inputbox">
                <input type="time" name="start_time" id="modify-start-time" required placeholder=" ">
                <label>Start Time</label>
            </div>
            <div class="inputbox">
                <input type="time" name="end_time" id="modify-end-time" required placeholder=" ">
                <label>End Time</label>
            </div>
            <div class="inputbox">
                <input type="url" name="onedrive_link" id="modify-onedrive-link" placeholder=" ">
                <label>OneDrive Link (Optional)</label>
            </div>
            <div class="inputbox">
                <input type="url" name="moodle_link" id="modify-moodle-link" placeholder=" ">
                <label>Moodle Link (Optional)</label>
            </div>
            <div class="inputbox">
                <input type="url" name="presto_link" id="modify-presto-link" placeholder=" ">
                <label>Presto Link (Optional)</label>
            </div>
            <div class="inputbox">
                <input type="url" name="maps_link" id="modify-maps-link" placeholder=" ">
                <label>Maps Link (Optional)</label>
            </div>
            <button type="submit" name="modify">Modify Lecture</button>
        </form>
    </div>


    <!-- Display all lectures -->
    <div class="lectures-list">
        <h2>Your Lectures</h2>
        <?php foreach ($lectures as $lecture): ?>
            <div class="lecture-item">
                <p><strong>Module Code:</strong> <?= htmlspecialchars($lecture['module_code']); ?></p>
                <p><strong>Module Name:</strong> <?= htmlspecialchars($lecture['module_name']); ?></p>
                <p><strong>Day:</strong> <?= htmlspecialchars($lecture['day']); ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($lecture['location']); ?></p>
                <p><strong>Start Time:</strong> <?= htmlspecialchars($lecture['start_time']); ?></p>
                <p><strong>End Time:</strong> <?= htmlspecialchars($lecture['end_time']); ?></p>
                <p><strong>OneDrive Link:</strong> <a href="<?= htmlspecialchars($lecture['onedrive_link']); ?>" target="_blank"><?= htmlspecialchars($lecture['onedrive_link']); ?></a></p>
                <p><strong>Moodle Link:</strong> <a href="<?= htmlspecialchars($lecture['moodle_link']); ?>" target="_blank"><?= htmlspecialchars($lecture['moodle_link']); ?></a></p>
                <p><strong>Presto Link:</strong> <a href="<?= htmlspecialchars($lecture['presto_link']); ?>" target="_blank"><?= htmlspecialchars($lecture['presto_link']); ?></a></p>
                <p><strong>Maps Link:</strong> <a href="<?= htmlspecialchars($lecture['maps_link']); ?>" target="_blank"><?= htmlspecialchars($lecture['maps_link']); ?></a></p>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="lecture_id" value="<?= htmlspecialchars($lecture['lecture_id']); ?>">
                    <button type="button" onclick="showModifyForm(<?= htmlspecialchars($lecture['lecture_id']); ?>)">Modify</button>
                    <button type="submit" name="delete">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>


    <script>
        function showModifyForm(lecture_id) {
            // Get the lecture data
            const lecture = <?= json_encode($lectures); ?>.find(lecture => lecture.lecture_id == lecture_id);

            // Populate the form with the lecture data
            document.getElementById('modify-lecture-id').value = lecture.lecture_id;
            document.getElementById('modify-module-code').value = lecture.module_code;
            document.getElementById('modify-module-name').value = lecture.module_name;
            document.getElementById('modify-day').value = lecture.day;
            document.getElementById('modify-location').value = lecture.location;
            document.getElementById('modify-start-time').value = lecture.start_time;
            document.getElementById('modify-end-time').value = lecture.end_time;
            document.getElementById('modify-onedrive-link').value = lecture.onedrive_link;
            document.getElementById('modify-moodle-link').value = lecture.moodle_link;
            document.getElementById('modify-presto-link').value = lecture.presto_link;
            document.getElementById('modify-maps-link').value = lecture.maps_link;

            // Show the modify form
            document.getElementById('modify-form').style.display = 'block';
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
    </script>
</body>
</html>