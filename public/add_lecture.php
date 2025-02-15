<?php
session_start();
require '../private/autoload.php'; 

// Check if user is logged in
$user_data = check_login($con);
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access. Please log in.");
}

// Handling form submission
// MODIFY THIS FORM FOR ONLY ICAL, NEED TO ADD THIS SORT OF FUNCTIONALITY IN
// THE ICS_PROCESSOR.PHP FILE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    // Retrieve form data
    $user_id = $_SESSION['user_id'];
    $module_code = trim($_POST['module_code']);
    $module_name = trim($_POST['module_name']);
    $day = trim(strtolower($_POST['day']));
    $location = trim($_POST['location']);
    $start_time = trim($_POST['start_time']);
    $end_time = trim($_POST['end_time']);
    $onedrive_link = !empty($_POST['onedrive_link']) ? trim($_POST['onedrive_link']) : null;
    $moodle_link = !empty($_POST['moodle_link']) ? trim($_POST['moodle_link']) : null;
    $presto_link = "https://attendance.kent.ac.uk/selfregistration";
    $maps_link = !empty($_POST['maps_link']) ? trim($_POST['maps_link']) : null;

    // Validate required fields
    if (empty($module_code) || empty($module_name) || empty($day) || empty($location) || empty($start_time) || empty($end_time)) {
        echo "Please fill in all required fields.";
    } else {
        // Insert into the database
        $query = "INSERT INTO lectures (user_id, module_code, module_name, day, location, start_time, end_time, onedrive_link, moodle_link, presto_link, maps_link) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $con->prepare($query);
        $stmt->bind_param(
            "issssssssss",
            $user_id,
            $module_code,
            $module_name,
            $day,
            $location,
            $start_time,
            $end_time,
            $onedrive_link,
            $moodle_link,
            $presto_link,
            $maps_link
        );

        if ($stmt->execute()) {
            echo "Lecture added successfully!";
            header('Location: view_lectures.php');
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        header('Location: view_lectures.php');
        exit;
    }
    
}





?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lecture</title>
    <link rel="stylesheet" href="timetable.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Adjust the form's position */

        body {
            background: url('image/WIP_image.jpg') no-repeat center center fixed;
            padding-top: 80px;

        }

    </style>
</head>
<body class="login-page">
    <section>
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
                <a href="add_lecture.php" class="active">Add Lecture</a>
                <a href="modify_lectures.php">Edit Lectures</a>
                <a href="logout.php">Log out</a>
            </div>
        </nav>

        <!-- Form to retrieve the ics link -->
        <div class="form-box">
            <div class="form-value">
                <form method="post">
                    <h2>Provide ICS webcal link</h2>
                    <p>Can find in the link below, in top right corner of page</p>
                    <a href="https://www.kent.ac.uk/student/my-study/">Click here</a>

                    <div class="inputbox">
                        <!-- Add functionality to accept the ical link -->
                        <input type="text" name="ical_link" required>
                        <label>ICAL link</label>
                    </div>


                    <button type="submit" name="add">Submit Link</button>
                </form>
            </div>
        </div>
    </section>


</body>
</html>






    
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>
</html>