<?php

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

    // Download the iCal data
    $ical_data = file_get_contents($ics_link);

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

function processICSFile($user_id, $filename) {
    $file_path = "../private/{$filename}";

    if (!file_exists($file_path)) {
        die("ICS file not found.");
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

        $events[] = $majorarray;
    }

    // Sort events into date order
    usort($events, function($a, $b) {
        return strtotime($a['DTSTART']) - strtotime($b['DTSTART']);
    });

    foreach ($events as $event) {
        $now = date('Y-m-d H:i:s'); // Current date and time
        $eventdate = date('Y-m-d H:i:s', strtotime($event['DTSTART'])); // User-friendly date

        if ($eventdate > $now) {
            echo "
                <div class='eventHolder'>
                    <div class='eventTitle'>".$event['COURSE_CODE']."</div>
                    <div class='eventDescription'>".$event['COURSE_TITLE']."</div>
                    <div class='eventLocation'>".$event['LOCATION']."</div>
                    <div class='eventDate'>$eventdate</div>
                    <br>
                </div>";
        }
    }
}


?>