<?php

define('REGISTRATION_FILE', 'registration_steps.json'); // File to store registration data
require_once __DIR__ . '/group_invitation.php';
require_once __DIR__ . '/database.php';

// Load registration steps from the JSON file
function loadRegistrationSteps() {
    if (!file_exists(REGISTRATION_FILE)) {
        return array();
    }
    $json = file_get_contents(REGISTRATION_FILE);
    return json_decode($json, true);
}

// Save registration steps to the JSON file
function saveRegistrationSteps($registrationSteps) {
    $json = json_encode($registrationSteps);
    file_put_contents(REGISTRATION_FILE, $json);
}

// Check if the user is in the middle of the registration process
function checkUserState($chat_id) {
    $registrationSteps = loadRegistrationSteps();
    return isset($registrationSteps[$chat_id]);
}

// Validate email format
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Validate name format (only letters)
function isValidName($name) {
    return preg_match('/^[a-zA-Z ]+$/', $name);
}

// Continue the registration process based on the current step
function continueRegistration($message) {
    // Load registration steps from file
    $registrationSteps = loadRegistrationSteps();

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'];

    if (isset($registrationSteps[$chat_id])) {
        $step = $registrationSteps[$chat_id]['step'];

        if ($step == 1) {
            // Check if the name is valid
            if (isValidName($text)) {
                $registrationSteps[$chat_id]['name'] = ucwords(strtolower($text)); // Capitalize the name
                $registrationSteps[$chat_id]['step'] = 2;
                saveRegistrationSteps($registrationSteps); // Save the state
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Cool. Now, please enter your email:'));
            } else {
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Please enter a valid name containing only letters.'));
            }
        } else if ($step == 2) {
            // Check if the email is valid
            if (isValidEmail($text)) {
                $registrationSteps[$chat_id]['email'] = strtolower($text); // Convert email to lowercase
                $registrationSteps[$chat_id]['step'] = 3;
                saveRegistrationSteps($registrationSteps); // Save the state
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Great. How many tickets do you need?'));
            } else {
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Please enter a valid email address.'));
            }
        } else if ($step == 3) {
            // Check if the input is a number
            if (is_numeric($text)) {
                $registrationSteps[$chat_id]['tickets'] = $text;
                $registrationSteps[$chat_id]['step'] = 4;
                saveRegistrationSteps($registrationSteps); // Save the state
                $name = $registrationSteps[$chat_id]['name'];
                $email = $registrationSteps[$chat_id]['email'];
                $tickets = $registrationSteps[$chat_id]['tickets'];
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Please confirm your registration:\nName: $name\nEmail: $email\nTickets: $tickets\n\nDo you want to proceed? Type 'yes' to confirm or 'no' to cancel."));
            } else {
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Please enter a valid number.'));
            }
        } else if ($step == 4) {
            // Check user confirmation
            if (strtolower($text) === 'yes') {
                $name = $registrationSteps[$chat_id]['name'];
                $email = $registrationSteps[$chat_id]['email'];
                $tickets = $registrationSteps[$chat_id]['tickets'];
                $uniqueID = uniqid();
                
                // Store user information in the database
                $userInfo = array(
                    'chat_id' => $chat_id,
                    'name' => $name,
                    'email' => $email,
                    'tickets' => $tickets,
                    'id' => $uniqueID
                );
                saveUserInfo($userInfo); // Function to save userInfo

                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Thank you, $name! Your request for $tickets tickets has been received. A confirmation has been sent to $email. Your unique ID is $uniqueID."));

                // Add user to group
                inviteUserToGroup($chat_id);

                // Reset the registration process for this user
                unset($registrationSteps[$chat_id]);
                saveRegistrationSteps($registrationSteps); // Save the state
            } else if (strtolower($text) === 'no') {
                // Cancel registration
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Registration process cancelled."));
                // Reset the registration process for this user
                unset($registrationSteps[$chat_id]);
                saveRegistrationSteps($registrationSteps); // Save the state
            } else {
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Please type 'yes' to confirm or 'no' to cancel."));
            }
        }
    }
}

function processRegistration($message) {
    // Load registration steps from file
    $registrationSteps = loadRegistrationSteps();

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];
    $text = $message['text'];

    if (strpos($text, "/register") === 0) {
        $registrationSteps[$chat_id] = array('step' => 1);
        saveRegistrationSteps($registrationSteps); // Save the state
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Please enter your name:'));
    } else if (checkUserState($chat_id)) {
        continueRegistration($message); // Handle registration continuation
    } else {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
    }
}

?>
