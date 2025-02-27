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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="settings_page.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings Page</title>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
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
                                <form>
                                    <div class="form-group">
                                        <label for="fullName">Username</label>
                                        <input type="text" class="form-control" id="fullName" value="Placeholder">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" value="xyz@gmail.com">
                                    </div>
                                    <div class="form-group">
                                        <label for="ics_link">ICS_link</label>
                                        <input type="text" class="form-control" id="ics_link" value="webcal://www.kent.ac.uk/timetabling/ics/2021/ics/ics_2021_22.ics">
                                    </div>
                                </form>
                                <div class="row mt-5">
                                    <div class="save_button">
                                        <button type="button" class="btn btn-primary btn-block">Save Changes</button>
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
                    <!-- Add your timetabling settings form here -->
                </div>
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
    </script>
    

</body>

</html>