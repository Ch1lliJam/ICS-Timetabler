<?php 
session_start();
require '../private/autoload.php'; 

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user_name = trim($_POST['user_name']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $ics_link = trim($_POST['ics_link']);

    // Email validation (stronger regex)
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/"; // Minimum 8 chars, 1 letter, 1 number, 1 special char
    $username_regex = "/^[a-zA-Z0-9_]{3,20}$/"; // Only alphanumeric and underscore, 3-20 chars
    

    // If all fields are provided and valid
    if (!empty($user_name) && !empty($password) && !empty($email) && !empty($ics_link) &&
        preg_match($email_regex, $email) &&
        preg_match($password_regex, $password) &&
        preg_match($username_regex, $user_name) &&
        validate_ics_link($ics_link)) {
        
        // Convert webcal to https
        $ics_link = str_replace("webcal://", "https://", $ics_link);
        
        // Check if username or email exists
        $query = "SELECT * FROM users WHERE user_name = ? OR email = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            echo "<script>alert('Username or email already exists.');</script>";
        } else {
            $user_id = getNextAvailableUserId();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (user_id, user_name, password, email, ics_link) VALUES (?, ?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("issss", $user_id, $user_name, $hashedPassword, $email, $ics_link);

            if ($stmt->execute()) {
                echo "<script>alert('Registration successful! Redirecting to login.'); window.location.href='loginpage.php';</script>";
                
            } else {
                echo "<script>alert('Error saving to database.');</script>";
            }
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please enter valid information.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="styles.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Page</title>
</head>
<body>
    
    
    <section>
        
        <nav>
            <label class="logo">ChilliJam</label>
            <ul>
                <li><a style="text-decoration: none" href="index.php">Home</a></li>
                <li><a style="text-decoration: none" class="active" href="loginpage.php">Login</a></li>
            </ul>
        </nav>

        <div class="form-box" style="height: 600px;">
            <div class="form-value">
                <form method="post" onsubmit="return validateForm(event, false)">
                    <h2>Create Account</h2>
                    <?php
                    if (!empty($error_message)) {
                        echo '<div class="error-message">' . htmlspecialchars($error_message) . '</div>';
                    }
                    ?>
					<div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" id="email" name="email" required>
                        <label for="">Email</label>
                    </div>
					
					<div class="inputbox">
                        <ion-icon name="person-outline"></ion-icon> 
                        <input id="user_name" type="text" name="user_name" required>
                        <label for="">Username</label>
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input id="password" type="password" name="password" required>
                        <label for="">Password</label>
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input id="ics_link" type="text" name="ics_link" required>
                        <label for="">ICS link</label>
                    </div>

                    
                    <button class="loginbutton" type="submit" value="Signup" >Sign up</button>
                    <div class="register">
                        <p>Have a account already?  &nbsp &nbsp<a href="loginpage.php">Login here</a></p>
                        <p>Can't find the ical link?  &nbsp &nbsp<a href="https://www.kent.ac.uk/student/my-study/">Go here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="validation.js"></script>

</body>
</html>