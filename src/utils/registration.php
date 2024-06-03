<?php

define('REGISTRATION_FILE', 'registration_steps.json'); // File to store registration data
require_once 'group_invitation.php';
require_once 'utils/database.php';


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

function processRegistration($message) {
    // Load registration steps from file
    $registrationSteps = loadRegistrationSteps();

    $message_id = $message['message_id'];
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {
        $text = $message['text'];

        if (strpos($text, "/register") === 0) {
            $registrationSteps[$chat_id] = array('step' => 1);
            saveRegistrationSteps($registrationSteps); // Save the state
            apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Please enter your name:'));
        } else if (isset($registrationSteps[$chat_id])) {
            $step = $registrationSteps[$chat_id]['step'];
            if ($step == 1) {
                $registrationSteps[$chat_id]['name'] = $text;
                $registrationSteps[$chat_id]['step'] = 2;
                saveRegistrationSteps($registrationSteps); // Save the state
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Cool. Now, please enter your email:'));
            } else if ($step == 2) {
                $registrationSteps[$chat_id]['email'] = $text;
                $registrationSteps[$chat_id]['step'] = 3;
                saveRegistrationSteps($registrationSteps); // Save the state
                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Great. How many tickets do you need?'));
            } else if ($step == 3) {
                $registrationSteps[$chat_id]['tickets'] = $text;
                $name = $registrationSteps[$chat_id]['name'];
                $email = $registrationSteps[$chat_id]['email'];
                $tickets = $registrationSteps[$chat_id]['tickets'];
                $uniqueID = uniqid();
                // store user information in the database
                $userInfo = array(
                    'chat_id' => $chat_id,
                    'name' => $name,
                    'email' => $email,
                    'tickets' => $tickets,
                    'id' => $uniqueID
                );
                saveUserInfo($userInfo); // function to save userInfo

                apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Thank you, $name! Your request for $tickets tickets has been received. A confirmation has been sent to $email. Your unique ID is $uniqueID."));

                // Add user to group
                inviteUserToGroup($chat_id);

                // Reset the registration process for this user
                unset($registrationSteps[$chat_id]);
                saveRegistrationSteps($registrationSteps); // Save the state
            }
        } else {
            apiRequestWebhook("sendMessage", array('chat_id' => $chat_id, "reply_to_message_id" => $message_id, "text" => 'Cool'));
        }
    } else {
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
    }
}

?>
