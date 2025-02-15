<?php 
session_start();
require '../private/autoload.php'; //yeah this should load stuff from the private folder, but it seems a bit faulty

	
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
                    <h2>Reset Password</h2>
					
					<div class="inputbox">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" id="email" name="email" required>
                        <label for="">Email</label>
                    </div>

                    
                    <button class="loginbutton" type="submit" name="submit" value="Signup" >Send email</button>
                </form>

                <?php
                    // Check if the form is submitted
                    if(isset($_POST['submit'])) {

                        // Connect to the database
                        $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
                        if ($con->connect_error) {
                            die("Connection failed: " . $con->connect_error);
                        }

                        // Get the inputted email
                        $email = $_POST['email'];

                        // Query the users table to check if the email exists
                        $sql = "SELECT * FROM users WHERE email = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            // Generate a unique token
                            $token = bin2hex(random_bytes(32));

                            // Save the token in the database
                            $sql = "INSERT INTO password_reset (email, token) VALUES (?, ?)";
                            $stmt = $con->prepare($sql);
                            $stmt->bind_param("ss", $email, $token);
                            $stmt->execute();

                            // Send email with the password reset link
                            $from = 'xyz@gmail.com'; // Your mailbox email address
                            $to = $email;
                            $subject = "Password Reset";
                            $reset_link = "http://example.com/reset_password.php?token=$token";
                            $message = "To reset your password, click on the link below:\n\n$reset_link";
                            $headers = "From: $from\r\n";
                            $headers .= "Reply-To: $from\r\n";
                            $headers .= "X-Mailer: PHP/" . phpversion();

                            // Set SMTP server settings
                            $smtpServer = 'smtp.hostingsite.com';
                            $smtpPort = 587; // or 465 for SSL/TLS
                            $smtpUsername = 'xyz@gmail.com'; // Your mailbox email address
                            $smtpPassword = 'placeholderPassword'; // Your mailbox password

                            // Set the SMTP server configuration
                            ini_set('SMTP', $smtpServer);
                            ini_set('smtp_port', $smtpPort);
                            ini_set('sendmail_from', $from);

                            // Set the SMTP authentication parameters
                            ini_set('smtp_auth', true);
                            ini_set('smtp_username', $smtpUsername);
                            ini_set('smtp_password', $smtpPassword);

                            // Send the email
                            if (mail($to, $subject, $message, $headers, "-f$from")) {
                                echo "An email has been sent to your email address with instructions to reset your password.";
                            } else {
                                echo "Failed to send email. Please try again later.";
                            }
                        } else {
                            echo "Email not found. Please check your email address.";
                        }


                    }
                    ?>


            </div>
        </div>
    </section>
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>