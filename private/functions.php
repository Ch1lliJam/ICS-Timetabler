<?php

function check_login($con)
{
	if(isset($_SESSION['user_id']))
	{
		
		$id = $_SESSION['user_id'];
		$query = "select * from users where user_id = '$id' limit 1";
		
		$result = mysqli_query($con,$query);
		if($result && mysqli_num_rows($result) > 0)
		{
			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}

	//redirect back to login
	header("Location: loginpage.php");
	die;
}

//function checking for next available user_id
function getNextAvailableUserId() {
    //query checking if anything already present in the user_id
    $sql = "SELECT user_id FROM users LIMIT 1";
    require 'connection.php';
	$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	$result = mysqli_query($con,$sql);

    //if no rows returned, then table is empty, so return 1 to start the table off
    if (mysqli_num_rows($result) == 0) {
        return 1;
    } else {
        //if rows returned, find the next user_id space free
        $maxQuery = "SELECT MAX(user_id) AS max_id FROM users";
        $maxResult = mysqli_query($con, $maxQuery);
        $row = mysqli_fetch_assoc($maxResult);
        $nextUserId = $row['max_id'] + 1;
        return $nextUserId;
    }
}

// Validate ICS link
function validate_ics_link($link) {
    $pattern = "/^webcal:\/\/www\.kent\.ac\.uk\/timetabling\/ical\/\d+\.ics$/";
    return preg_match($pattern, $link);
}

function get_next20_lectures($con, $user_id, $limit) {
    $start_point = $limit - 20;
    $query = "SELECT * FROM lectures WHERE user_id = ? ORDER BY day, start_time LIMIT 20 OFFSET ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $user_id, $start_point);
    $stmt->execute();
    $result = $stmt->get_result();
    $lectures = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return json_encode($lectures);
}