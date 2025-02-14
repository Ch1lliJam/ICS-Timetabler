<?php
function iCalDecoder($file) {
        $ical = file_get_contents($file);
        preg_match_all('/(BEGIN:VEVENT.*?END:VEVENT)/si', $ical, $result, PREG_PATTERN_ORDER);
        for ($i = 0; $i < count($result[0]); $i++) {
            $tmpbyline = explode("\r\n", $result[0][$i]);

            foreach ($tmpbyline as $item) {
                $tmpholderarray = explode(":",$item);
                if (count($tmpholderarray) >1) {
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

            $icalarray[] = $majorarray;
            unset($majorarray);

        }
        return $icalarray;
}



//read events
$events = iCalDecoder("cal_data.ics");

//sort events into date order
usort($events, function($a, $b) {
    return strtotime($a['DTSTART']) - strtotime($b['DTSTART']);
});

foreach($events as $event){
    $now = date('Y-m-d H:i:s'); //current date and time
    $eventdate = date('Y-m-d H:i:s', strtotime($event['DTSTART'])); //user friendly date

    if($eventdate > $now){
        echo "
            <div class='eventHolder'>
                <div class='eventTitle'>".$event['COURSE_CODE']."</div>
                <div class='eventDescription'>".$event['COURSE_TITLE']."</div>
                <div class='eventDate'>$eventdate</div>
                <br>
            </div>";
    }
}
?>