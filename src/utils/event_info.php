<?php

// Define event information
$eventDetails = array(
    'name' => 'Tech Conference 2024',
    'date' => '2024-08-15',
    'time' => '10:00 AM',
    'location' => 'Tech Convention Center, San Francisco',
    'description' => 'A premier conference showcasing the latest in technology and innovation.',
);

// Function to get event details
function getEventDetails() {
    global $eventDetails;
    $details = "Event Name: " . $eventDetails['name'] . "\n";
    $details .= "Date: " . $eventDetails['date'] . "\n";
    $details .= "Time: " . $eventDetails['time'] . "\n";
    $details .= "Location: " . $eventDetails['location'] . "\n";
    $details .= "Description: " . $eventDetails['description'];
    return $details;
}

// Function to update event details
function updateEventDetails($name, $date, $time, $location, $description) {
    global $eventDetails;
    $eventDetails['name'] = $name;
    $eventDetails['date'] = $date;
    $eventDetails['time'] = $time;
    $eventDetails['location'] = $location;
    $eventDetails['description'] = $description;
}

// Function to handle incoming messages related to event info
function processEventInfoMessage($message) {
    $chat_id = $message['chat']['id'];
    $text = $message['text'];

    if (strpos($text, "/eventinfo") === 0) {
        $eventDetails = getEventDetails();
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $eventDetails));
    } elseif (strpos($text, "/updateevent") === 0) {
        // For simplicity, assuming the update command is in the format:
        // /updateevent name,date,time,location,description
        $parts = explode(",", substr($text, strlen("/updateevent ")));
        if (count($parts) == 5) {
            updateEventDetails($parts[0], $parts[1], $parts[2], $parts[3], $parts[4]);
            apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Event details updated successfully."));
        } else {
            apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Invalid format. Please use /updateevent name,date,time,location,description"));
        }
    } else {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Invalid command. Use /eventinfo to get event details."));
    }
}

?>
