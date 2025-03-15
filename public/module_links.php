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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['modules'] as $module_code => $links) {
        if (!empty($module_code)) {
            $onedrive_link = trim($links['onedrive_link']) ?: 'https://livekentac-my.sharepoint.com/my';
            $moodle_link = trim($links['moodle_link']) ?: 'https://moodle.kent.ac.uk/2024/my/courses.php';
            $picture_link = trim($links['picture_link']) ?: '';

            // Validate inputs
            if (!filter_var($onedrive_link, FILTER_VALIDATE_URL) || !filter_var($moodle_link, FILTER_VALIDATE_URL) || !filter_var($picture_link, FILTER_VALIDATE_URL)) {
                echo "<script>alert('Invalid URL format for module code: " . htmlspecialchars($module_code) . "');</script>";
                continue;
            }

            $query = "INSERT INTO module_links (user_id, module_code, onedrive_link, moodle_link, picture_link) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("issss", $user_id, $module_code, $onedrive_link, $moodle_link, $picture_link);
            $stmt->execute();
        }
    }

    // Clear new modules from session
    unset($_SESSION['new_modules']);

    // Redirect to view lectures page
    header("Location: view_lectures.php");
    exit;
}

// Get new modules from session
$new_modules = $_SESSION['new_modules'] ?? [];

// Filter out duplicate module codes
$unique_modules = array_unique(array_filter($new_modules));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ch1lliJam | Module Links</title>
    <link rel="stylesheet" href="timetable.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="validation.js"></script>
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

            <a class="home-link" href="#">ChilliJam</a>
            <a href="logout.php">Log out</a>
        </div>
    </nav>

    <!-- Module Links Form -->
    <div class="lectures-list">
        <h2>Enter Links for New Modules</h2>
        <form method="post" onsubmit="return validateModuleLinksForm(event)">
            <?php foreach ($unique_modules as $module_code): ?>
                <div class="lecture-item">
                    <h3><?php echo htmlspecialchars($module_code); ?></h3>
                    <div class="inputbox">
                        <label for="onedrive_link_<?php echo $module_code; ?>">OneDrive Link:</label>
                        <input type="text" id="onedrive_link_<?php echo $module_code; ?>" name="modules[<?php echo $module_code; ?>][onedrive_link]" placeholder="https://livekentac-my.sharepoint.com/my">
                    </div>
                    <div class="inputbox">
                        <label for="moodle_link_<?php echo $module_code; ?>">Moodle Link:</label>
                        <input type="text" id="moodle_link_<?php echo $module_code; ?>" name="modules[<?php echo $module_code; ?>][moodle_link]" placeholder="https://moodle.kent.ac.uk/2024/my/courses.php">
                    </div>
                    <div class="inputbox">
                        <label for="picture_link_<?php echo $module_code; ?>">Picture Link:</label>
                        <input type="text" id="picture_link_<?php echo $module_code; ?>" name="modules[<?php echo $module_code; ?>][picture_link]" placeholder="https://example.com/image.jpg">
                    </div>
                </div>
            <?php endforeach; ?>
            <button type="submit" class="module-links-btn">Save Links</button>
        </form>
    </div>
</body>
</html>