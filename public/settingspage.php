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


$query = "SELECT user_name, email, ics_link FROM users WHERE user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['reset_timetable'])) {
        // Remove all entries in module_links and lectures for the specific user_id
        $delete_module_links_query = "DELETE FROM module_links WHERE user_id = ?";
        $stmt = $con->prepare($delete_module_links_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $delete_lectures_query = "DELETE FROM lectures WHERE user_id = ?";
        $stmt = $con->prepare($delete_lectures_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Log the user out
        session_destroy();
        echo "<script>alert('Your timetable data has been reset. You will now be logged out.'); window.location.href = 'loginpage.php';</script>";
        exit;
    } elseif (isset($_POST['update_module'])) {
        $module_code = trim($_POST['edit_module_code']);
        $onedrive_link = trim($_POST['edit_onedrive_link']);
        $moodle_link = trim($_POST['edit_moodle_link']);
        $picture_link = trim($_POST['edit_picture_link']);
        $location_link = trim($_POST['edit_location_link']);

        $update_query = "UPDATE module_links SET onedrive_link = ?, moodle_link = ?, picture_link = ?, location_link = ? WHERE user_id = ? AND module_code = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("sssssi", $onedrive_link, $moodle_link, $picture_link, $location_link, $user_id, $module_code);
        $stmt->execute();

        header("Location: settingspage.php");
        exit;
    } else {
        $user_name = trim($_POST['user_name']);
        $email = trim($_POST['email']);
        $ics_link = trim($_POST['ics_link']);

        $update_query = "UPDATE users SET user_name = ?, email = ?, ics_link = ? WHERE user_id = ?";
        $stmt = $con->prepare($update_query);
        $stmt->bind_param("sssi", $user_name, $email, $ics_link, $user_id);
        $stmt->execute();

        header("Location: settingspage.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="settings_page.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Page</title>
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons/ionicons.esm.min.js"></script>
    <script nomodule src="https://cdnjs.cloudflare.com/ajax/libs/ionicons/5.5.2/ionicons/ionicons.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body class="settings_page">

    <nav>
        <input type="checkbox" id="sidebar-active">
        <label for="sidebar-active" class="open-sidebar-button">
            <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32">
                <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z" />
            </svg>
        </label>
        <label id="overlay" for="sidebar-active"></label>
        <div class="links-container">
            <label for="sidebar-active" class="close-sidebar-button">
                <svg xmlns="http://www.w3.org/2000/svg" height="32" viewBox="0 -960 960 960" width="32">
                    <path
                        d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224-224 224Z" />
                </svg>
            </label>

            <a class="home-link" href="view_lectures.php">ChilliJam</a>
            <a href="view_lectures.php">Lectures</a>
            <a href="modify_lectures.php">Edit Lectures</a>
            <a class="active" href="settingspage.php">Settings</a>
            <a href="logout.php">Log out</a>
        </div>
    </nav>


    <div class="container-fluid main" style="height:100vh;padding-left:0px;">
        <div class="row" style="height:100%">
            <div class="leftside_nav" style="height:100%">
                <div class="container-fluid nav sidebar flex-column">
                    <a href="#" class="nav-link active" onclick="showSection('profile')"><ion-icon name="person-circle-outline"></ion-icon> Profile</a>
                    <a href="#" class="nav-link" onclick="showSection('appearance')"><ion-icon name="settings-outline"></ion-icon> Appearance</a>
                    <a href="#" class="nav-link" onclick="showSection('timetabling')"><ion-icon name="document-text-outline"></ion-icon> Timetabling</a>
                </div>
            </div>

            <div class="profile_settings section" id="profile">
                <div class="container content clear-fix">
                    <h2 class="mt-5 mb-5">Profile Settings</h2>
                    <div class="row" style="height:100%">
                        <div class="profile_form">
                            <div class="profile_form_container">
                                <form method="POST" action="settingspage.php">
                                    <div class="form-group">
                                        <label for="user_name">Username</label>
                                        <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo htmlspecialchars($user['user_name']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="ics_link">ICS Link</label>
                                        <input type="text" class="form-control" id="ics_link" name="ics_link" value="<?php echo htmlspecialchars($user['ics_link']); ?>">
                                    </div>
                                    <div class="row mt-5">
                                        <div class="save_button">
                                            <button type="submit" class="btn btn-primary btn-block">Save Changes</button>
                                        </div>
                                        <div class="cancel_button">
                                            <button type="button" class="btn btn-default btn-block" onclick="resetForm()">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="appearance_settings section" id="appearance" style="display:none;">
                <div class="container content clear-fix">
                    <h2 class="mt-5 mb-5">Appearance Settings</h2>
                    <div class="row" style="height:100%">
                        <div class="appearance_form">
                            <div class="appearance_form_container">
                                <form>
                                    <div class="form-group">
                                        <label for="navbarColor">Navbar Color</label>
                                        <input type="color" class="form-control" id="navbarColor" value="#6c63ff">
                                    </div>
                                    <div class="form-group">
                                        <label for="backgroundColor">Background Color</label>
                                        <input type="color" class="form-control" id="backgroundColor" value="#ffffff">
                                    </div>
                                    <div class="form-group">
                                        <label for="settingsNavbarColor">Settings Page Navbar Color</label>
                                        <input type="color" class="form-control" id="settingsNavbarColor" value="#6c63ff">
                                    </div>
                                    <div class="form-group">
                                        <label for="navbarTextColor">Navbar Text Color</label>
                                        <input type="color" class="form-control" id="navbarTextColor" value="#ffffff">
                                    </div>
                                    <div class="form-group">
                                        <label for="settingsNavbarTextColor">Settings Page Navbar Text Color</label>
                                        <input type="color" class="form-control" id="settingsNavbarTextColor"
                                            value="#ffffff">
                                    </div>
                                    <div class="form-group">
                                        <label for="mainPageTextColor">Main Page Text Color</label>
                                        <input type="color" class="form-control" id="mainPageTextColor" value="#000000">
                                    </div>
                                </form>
                                <div class="row mt-5">
                                    <div class="save_button">
                                        <button type="button" class="btn btn-primary btn-block"
                                            onclick="applyChanges()">Apply Changes</button>
                                    </div>
                                    <div class="cancel_button">
                                        <button type="button" class="btn btn-default btn-block">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="timetabling_settings section" id="timetabling" style="display:none;">
                <div class="container content clear-fix">
                    <h2 class="mt-5 mb-5">Timetabling Settings</h2>

                    <form method="POST" action="settingspage.php">
                        <div class="form-group">
                            <button type="submit" name="reset_timetable" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to reset your timetable data? This action cannot be undone. You will be logged out.');">Reset Timetable Data</button>
                        </div>
                    </form>


                </div>

            </div>
        </div>



        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
            integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
            crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>

        <script>
            function showSection(sectionId) {
                // Hide all sections
                document.querySelectorAll('.section').forEach(section => {
                    section.style.display = 'none';
                });

                // Show the selected section
                const targetSection = document.getElementById(sectionId);
                if (targetSection) {
                    targetSection.style.display = 'flex';
                } else {
                    console.error(`Section with ID '${sectionId}' not found.`);
                }

                // Remove active class from all navbar links
                document.querySelectorAll('.leftside_nav .nav-link').forEach(link => {
                    link.classList.remove('active');
                });

                // Add active class to the clicked navbar link
                document.querySelector(`.leftside_nav .nav-link[onclick="showSection('${sectionId}')"]`).classList.add('active');
            }

            function applyChanges() {
                const navbarColor = document.getElementById('navbarColor').value;
                const backgroundColor = document.getElementById('backgroundColor').value;
                const settingsNavbarColor = document.getElementById('settingsNavbarColor').value;
                const navbarTextColor = document.getElementById('navbarTextColor').value;
                const settingsNavbarTextColor = document.getElementById('settingsNavbarTextColor').value;
                const mainPageTextColor = document.getElementById('mainPageTextColor').value;

                document.querySelector('nav').style.backgroundColor = navbarColor;
                document.body.style.backgroundColor = backgroundColor;
                document.querySelector('.leftside_nav').style.backgroundColor = settingsNavbarColor;
                document.querySelectorAll('nav a').forEach(link => {
                    link.style.color = navbarTextColor;
                });
                document.querySelectorAll('.leftside_nav .nav-link').forEach(link => {
                    link.style.color = settingsNavbarTextColor;
                });
                document.body.style.color = mainPageTextColor;
            }

            function resetForm() {
                document.getElementById('user_name').value = "<?php echo htmlspecialchars($user['user_name']); ?>";
                document.getElementById('email').value = "<?php echo htmlspecialchars($user['email']); ?>";
                document.getElementById('ics_link').value = "<?php echo htmlspecialchars($user['ics_link']); ?>";
            }

        </script>


</body>

</html>