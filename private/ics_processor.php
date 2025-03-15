<?php
require 'connection.php';
function downloadICSFile($user_id, $con) {
    // Retrieve the ics_link from the database
    $query = "SELECT ics_link FROM users WHERE user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $ics_link = $user_data['ics_link'];
    } else {
        die("ICS link not found for the user.");
    }
    $stmt->close();

    // Download the iCal data using cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ics_link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification

    $ical_data = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    if ($ical_data === false) {
        die("Failed to download iCal data.");
    }

    // Define the file path
    $ics_file_path = "../private/{$user_id}.ics";

    // Check if the file already exists and compare the content
    if (file_exists($ics_file_path)) {
        $existing_data = file_get_contents($ics_file_path);
        if ($existing_data === $ical_data) {
            // Data has not changed, no need to write to the file
            return $ics_file_path;
        }
    }

    // Save the iCal data to a file (overwrite existing content)
    if (file_put_contents($ics_file_path, $ical_data) === false) {
        die("Failed to save iCal data to file.");
    }

    return $ics_file_path;
}

function processICSFile($user_id, $filename, $con) {
    $file_path = "../private/{$filename}";

    if (!file_exists($file_path)) {
        return false;
    }

    $ical = file_get_contents($file_path);
    preg_match_all('/(BEGIN:VEVENT.*?END:VEVENT)/si', $ical, $result, PREG_PATTERN_ORDER);
    $events = [];
    for ($i = 0; $i < count($result[0]); $i++) {
        $tmpbyline = explode("\r\n", $result[0][$i]);
        $majorarray = [];

        foreach ($tmpbyline as $item) {
            $tmpholderarray = explode(":", $item);
            if (count($tmpholderarray) > 1) {
                $majorarray[$tmpholderarray[0]] = $tmpholderarray[1];
            }
        }

        if (preg_match('/DESCRIPTION:(.*)END:VEVENT/si', $result[0][$i], $regs)) {
            $majorarray['DESCRIPTION'] = str_replace("  ", " ", str_replace("\r\n", "", $regs[1]));
        }

        // Split the SUMMARY field into individual components
        if (isset($majorarray['SUMMARY'])) {
            $summaryParts = explode(' ', $majorarray['SUMMARY'], 3);
            $majorarray['LECTURE_TYPE'] = $summaryParts[0] ?? '';
            $majorarray['COURSE_CODE'] = $summaryParts[1] ?? '';
            $majorarray['COURSE_TITLE'] = $summaryParts[2] ?? '';
        }

        // Extract the LOCATION field
        if (isset($majorarray['LOCATION'])) {
            $locationParts = explode(' ', $majorarray['LOCATION'], 1);
            $majorarray['LOCATION'] = $locationParts[0] ?? '';
        }


        // Extract the DTSTART and DTEND fields
        if (isset($majorarray['DTSTART'])) {
            $startDateTime = DateTime::createFromFormat('Ymd\THis', $majorarray['DTSTART']);
            $majorarray['START_DATE'] = $startDateTime->format('Y-m-d');
            $majorarray['START_TIME'] = $startDateTime->format('H:i');
        }

        if (isset($majorarray['DTEND'])) {
            $endDateTime = DateTime::createFromFormat('Ymd\THis', $majorarray['DTEND']);
            $majorarray['END_DATE'] = $endDateTime->format('Y-m-d');
            $majorarray['END_TIME'] = $endDateTime->format('H:i');
        }

        $events[] = $majorarray;
    }

    // Sort events into date order
    usort($events, function($a, $b) {
        return strtotime($a['DTSTART']) - strtotime($b['DTSTART']);
    });

    // Remove old lectures
    removeOldLectures($user_id, $con);

    // Remove past lectures and add new ones
    $newEvents = checkForNewLectures($user_id, $con, $events);

    if (count($newEvents) > 0) {
        $currentDateTime = new DateTime(); // alternative idea to fetch lectures that are still happening
        $currentDateTime->modify('-1 day');

        foreach ($newEvents as $event) {
            $eventEndDateTime = DateTime::createFromFormat('Y-m-d H:i', $event['END_DATE'] . ' ' . ($event['END_TIME']));

            // Check if the event is on the same day and if the end time has not passed
            if ($eventEndDateTime > $currentDateTime) {
                $module_code = $event['COURSE_CODE'];
                $module_name = $event['COURSE_TITLE'];
                $day = $event['START_DATE'];
                $location = $event['LOCATION'];
                $start_time = $event['START_TIME'];
                $end_time = $event['END_TIME'];
                $onedrive_link = 'https://livekentac-my.sharepoint.com/my';
                $moodle_link = 'https://moodle.kent.ac.uk/2024/my/courses.php';
                $presto_link = 'https://attendance.kent.ac.uk/selfregistration';
                $maps_link = 'https://www.google.com/maps/search/?api=1&query='.$location;

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

                if (!$stmt->execute()) {
                    echo "Error: " . $stmt->error;
                    $stmt->close();
                    return false;
                }
                $stmt->close();
            }
        }
        $new_modules = [];
        foreach ($newEvents as $event) {
            $module_code = $event['COURSE_CODE'];
            $query = "SELECT * FROM module_links WHERE user_id = ? AND module_code = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("is", $user_id, $module_code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows == 0) {
                $new_modules[] = $module_code;
            }
        }

        // Store new modules in session
        $_SESSION['new_modules'] = $new_modules;
        
        return true;
    } else {
        return true;
    }
}

function checkForNewLectures($user_id, $con, $events) {
    // Get the last lecture date for the user
    $query = "SELECT MAX(day) AS last_lecture FROM lectures WHERE user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $last_lecture = $user_data['last_lecture'];
    } else {
        $last_lecture = null;
    }
    $stmt->close();

    // Filter out events that are before the last lecture date
    $newEvents = [];
    foreach ($events as $event) {
        $eventDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $event['START_DATE'] . ' ' . $event['START_TIME']);
        if ($last_lecture === null || $eventDateTime > new DateTime($last_lecture)) {
            $newEvents[] = $event;
        }
    }

    return $newEvents;
}


//function to remove lectures before the current date and time
function removeOldLectures($user_id, $con) {
    // Get the current date and time
    $currentDateTime = date('Y-m-d H:i', strtotime("-1 hour"));

    // Query to delete lectures before the current date and time for the specified user_id
    $query = "DELETE FROM lectures WHERE user_id = ? AND STR_TO_DATE(CONCAT(day, ' ', end_time), '%Y-%m-%d %H:%i') < ?";
    $stmt = $con->prepare($query);

    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }

    $stmt->bind_param("is", $user_id, $currentDateTime);

    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $result = $stmt->affected_rows;
    $stmt->close();

    return $result;
}

