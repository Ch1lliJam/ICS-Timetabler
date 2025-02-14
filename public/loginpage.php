<?php
// filepath: /d:/Xampp/htdocs/mini website new approach/public/loginpage.php

session_start();
require '../private/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Retrieve and sanitize input
    $user_name = trim($_POST['user_name']);
    $password = trim($_POST['password']);

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        // Use prepared statements to prevent SQL injection
        $query = "SELECT * FROM users WHERE user_name = ? LIMIT 1";
        $stmt = $con->prepare($query);
        $stmt->bind_param("s", $user_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $verify = password_verify($password, $user_data['password']); // Hashing check

            if ($verify) {
                $_SESSION['user_id'] = $user_data['user_id'];
                header("Location: view_lectures.php");
                die;
            } else {
                echo '<script type="text/javascript">
                alert("Invalid username or password.");
                </script>';
            }
        } else {
            echo '<script type="text/javascript">
            alert("Invalid username or password.");
            </script>';
        }
        $stmt->close();
    } else {
        echo '<script type="text/javascript">
        alert("Please enter some valid information.");
        </script>';
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
            <label class="logo" href="index.php">ChilliJam</label>
            <ul>
                <li><a style="text-decoration: none" href="index.php">Home</a></li>
                <li><a style="text-decoration: none" class="active" href="loginpage.php">Login</a></li>
            </ul>
        </nav>

        <div class="form-box">
            <div class="form-value">
                <form method="post">
                    
                    <h2>Login</h2>

                    <div class="inputbox">
                        <ion-icon name="person-outline"></ion-icon>
                        <input type="text" id="user_name" name="user_name" required>
                        <label for="">Username</label>
                    </div>

                    
                    <div class="inputbox">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="passwordID" name="password" required >
                        <label for="">Password</label>
                    </div>
                    

                    <div class="forget">
                        <label for=""><input type="checkbox">Remember Me &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp<a href="resetPassword.php">Forget Password</a></label>
                      
                    </div>
                    <button class="loginbutton" type="submit" value="Login">Log in</button>
                    
                    <div class="register">
                        <p>Don't have a account?  &nbsp &nbsp<a href="registerpage.php">Register</a></p>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>