<?php
// Example usage
session_start();
require '../private/autoload.php';

// Check if the user is logged in
$user_data = check_login($con);
if (!$user_data) {
    header("Location: login.html");
    exit;
}

$user_id = $user_data['user_id'];
$filename = "{$user_id}.ics";

// Download the ICS file
downloadICSFile($user_id, $con);

// Process the ICS file
processICSFile($user_id, $filename, $con);

// Redirect to the lectures page
header('Location: view_lectures.php');



?>