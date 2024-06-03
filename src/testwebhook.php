<?php

define('BOT_TOKEN', '7245509649:AAFmNUn07iZaMZfroczuoK6CKEV0ZR1kXW0');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('REGISTRATION_FILE', 'registration_steps.json'); // File to store registration data

function apiRequestWebhook($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $payload = json_encode($parameters);
  header('Content-Type: application/json');
  header('Content-Length: '.strlen($payload));
  echo $payload;

  return true;
}

function exec_curl_request($handle) {
  $response = curl_exec($handle);

  if ($response === false) {
    $errno = curl_errno($handle);
    $error = curl_error($handle);
    error_log("Curl returned error $errno: $error\n");
    curl_close($handle);
    return false;
  }

  $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
  curl_close($handle);

  if ($http_code >= 500) {
    sleep(10);
    return false;
  } else if ($http_code != 200) {
    $response = json_decode($response, true);
    error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
    if ($http_code == 401) {
      throw new Exception('Invalid access token provided');
    }
    return false;
  } else {
    $response = json_decode($response, true);
    if (isset($response['description'])) {
      error_log("Request was successful: {$response['description']}\n");
    }
    $response = $response['result'];
  }

  return $response;
}

function apiRequest($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  foreach ($parameters as $key => &$val) {
    if (!is_numeric($val) && !is_string($val)) {
      $val = json_encode($val);
    }
  }
  $url = API_URL.$method.'?'.http_build_query($parameters);

  $handle = curl_init($url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);

  return exec_curl_request($handle);
}

function apiRequestJson($method, $parameters) {
  if (!is_string($method)) {
    error_log("Method name must be a string\n");
    return false;
  }

  if (!$parameters) {
    $parameters = array();
  } else if (!is_array($parameters)) {
    error_log("Parameters must be an array\n");
    return false;
  }

  $parameters["method"] = $method;

  $handle = curl_init(API_URL);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($handle, CURLOPT_TIMEOUT, 60);
  curl_setopt($handle, CURLOPT_POST, true);
  curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
  curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

  return exec_curl_request($handle);
}

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

function processMessage($message) {
  // Load registration steps from file
  $registrationSteps = loadRegistrationSteps();

  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  if (isset($message['text'])) {
    $text = $message['text'];

    if (strpos($text, "/start") === 0) {
      apiRequestJson("sendMessage", array('chat_id' => $chat_id, "text" => 'Hello! How can I assist you today?', 'reply_markup' => array(
        'keyboard' => array(
          array('/start', '/help'),
          array('/register')
        ),
        'one_time_keyboard' => true,
        'resize_keyboard' => true)));
    } else if ($text === "Hello" || $text === "Hi") {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'Nice to meet you'));
    } else if (strpos($text, "/help") === 0) {
      $helpMessage = "Usage:\n";
      $helpMessage .= "/start - Start interacting with the bot\n";
      $helpMessage .= "/help - Provides help and usage instructions\n";
      $helpMessage .= "/register - Register for the event\n";
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $helpMessage));
    } else if (strpos($text, "/register") === 0) {
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

        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Thank you, $name! Your request for $tickets tickets has been received. A confirmation has been sent to $email. Your unique ID is $uniqueID."));

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

define('WEBHOOK_URL', 'https://wijewardeneindustries.com/EventTicketingTelegramBot/index.php');

if (php_sapi_name() == 'cli') {
  apiRequest('setWebhook', array('url' => isset($argv[1]) && $argv[1] == 'delete' ? '' : WEBHOOK_URL));
  exit;
}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
  exit;
}

if (isset($update["message"])) {
  processMessage($update["message"]);
}


?>


