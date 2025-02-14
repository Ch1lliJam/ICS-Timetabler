<?php 
// filepath: /d:/Xampp/htdocs/mini website new approach/public/registerpage.php

session_start();
require '../private/autoload.php'; // Ensure this file includes database connection and the `check_login` function

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve and sanitize input
    $user_name = trim($_POST['user_name']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Validate email with improved regex
    if (!empty($user_name) && !empty($password) && !empty($email) && preg_match("/^[\w\-]+@[\w\-]+\.[\w\-\.]+$/", $email)) {
        // Check if username or email already exists
        $query = "SELECT * FROM users WHERE user_name = ? OR email = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Save to database
            $user_id = getNextAvailableUserId();
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $query = "INSERT INTO users (user_id, user_name, password, email) VALUES (?, ?, ?, ?)";
            $stmt = $con->prepare($query);
            $stmt->bind_param("isss", $user_id, $user_name, $hashedPassword, $email);

            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: loginpage.php");
                die;
            } else {
                $error_message = "Error saving to database.";
            }
        }
        $stmt->close();
    } else {
        // Determine specific error message
        if (empty($user_name)) {
            $error_message = "Username cannot be empty.";
        } elseif (empty($password)) {
            $error_message = "Password cannot be empty.";
        } elseif (empty($email)) {
            $error_message = "Email cannot be empty.";
        } elseif (!preg_match("/^[\w\-]+@[\w\-]+\.[\w\-\.]+$/", $email)) {
            $error_message = "Invalid email format.";
        } else {
            $error_message = "Please enter valid information.";
        }
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

        <div class="form-box">
            <div class="form-value">
                <form method="post">
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
                        <input id="text" type="text" name="user_name" required>
                        <label for="">Username</label>
                    </div>
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input id="text" type="password" name="password" required>
                        <label for="">Password</label>
                    </div>

                    
                    <button class="loginbutton" type="submit" value="Signup" >Sign up</button>
                    <div class="register">
                        <p>Have a account already?  &nbsp &nbsp<a href="loginpage.php">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>