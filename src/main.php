<?php

//$config = json_decode(file_get_contents('config.json'), true);

$configFile =  __DIR__ . '/config.json';

// Check if the config file exists
if (!file_exists($configFile)) {
  die("Error: config.json file not found!");
}

// Load and decode the config file
$configContent = file_get_contents($configFile);
$config = json_decode($configContent, true);

// Check for JSON errors
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON in config.json");
}

// Access configs from config.json
define('BOT_TOKEN', $config['bot_token']);
define('BOT_USERNAME', $config['bot_username']);
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define("WEBHOOK_URL", $config['webhook_url']);

// Require other functionalities 
require_once 'utils/registration.php';
require_once 'utils/event_info.php';
require_once 'utils/group_invitation.php';
require_once 'utils/database.php';

// Setting webhook URL
$webhook_api = API_URL . 'setWebhook?url=' . WEBHOOK_URL;
$webhook_response = file_get_contents($webhook_api);
echo $webhook_response;

echo "<br/> \n\n";

// Get webhook URL info
$webhook_info_url = API_URL . 'getWebhookInfo';
$webhook_info_response = file_get_contents($webhook_info_url);
$webhook_info = json_decode($webhook_info_response, true);

echo $webhook_info_response;

// Set bot commands
$commands = [
    ['command' => 'start', 'description' => 'Start the bot'],
    ['command' => 'help', 'description' => 'Bot use instructions'],
    ['command' => 'register', 'description' => 'Register for the event'],
    ['command' => 'eventinfo', 'description' => 'Event information']
];

$setCommandsPayload = [
    'commands' => json_encode($commands)
];

$setCommandsUrl = "https://api.telegram.org/bot".BOT_TOKEN."/setMyCommands";

$ch = curl_init($setCommandsUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $setCommandsPayload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

if ($result === false) {
    error_log("Error setting commands: " . curl_error($ch));
} else {
    echo "Commands set successfully!";
}

// END Command setting

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

function startCommands($chat_id, $first_name) {
    
    $greeting = "Tell me $first_name! How can I assist you today? ☺️";
    
    apiRequestJson("sendMessage", array(
        'chat_id' => $chat_id,
        'text' => $greeting,
        'reply_markup' => array(
            'inline_keyboard' => array(
                array(
                    array('text' => 'Help', 'callback_data' => 'help'),
                    array('text' => 'Event Info', 'callback_data' => 'eventInfo'),
                ),
                array(
                    array('text' => 'Register', 'callback_data' => 'register')
                ),
                // array(
                //     array('text' => 'Event Info', 'callback_data' => 'eventInfo')
                // )
            )
        )
    ));
}

function sendHelpMessage($chat_id,$first_name) {
    
    $greeting = "Tell me $first_name! How can I assist you today? ☺️";
    $helpMessage = "Here are the commands you can use:\n";
    
    apiRequestJson("sendMessage", array(
        'chat_id' => $chat_id,
        'text' =>   $greeting. "\n". $helpMessage ."\n",
        'reply_markup' => json_encode(array(
            'inline_keyboard' => [
                [['text' => '📅 Start - Greet & Event Details', 'callback_data' => 'start']],
                [['text' => '📝 Register - Register for the event', 'callback_data' => 'register']],
                [['text' => 'ℹ️ Help - Bot use instructions', 'callback_data' => 'help']],
                // [['text' => '📋 Event Info', 'callback_data' => 'eventInfo']]
            ]
        ))
    ));
}

function processMessage($message) {
  $message_id = $message['message_id'];
  $chat_id = $message['chat']['id'];
  $first_name = $message['chat']['first_name']; // Get the user's first name
  if (isset($message['text'])) {
    $text = $message['text'];

    if (strpos($text, "/start") === 0) {
        
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" =>  "Hello $first_name! How are you doing? 😄. Below are the new event details 👏"));
        processEventInfoMessage($message);
        //sendHelpMessage($chat_id, $first_name);
        
        // Then show commands links
        // startCommands($chat_id,$first_name);
        
    } else if ($text === "Hello" || $text === "Hi" || $text === "hi" || $text === "hello") {
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Hi $first_name! Nice to meet you ☺️ "));
    } else if (strpos($text, "/help") === 0) {
        
        //$helpMessage = "Here are the commands you can use 👇:\n";
        
        sendHelpMessage($chat_id, $first_name); // Call /help command function
        
        // apiRequestJson("sendMessage", array(
        //     'chat_id' => $chat_id,
        //     'text' => $helpMessage . sendHelpMessage($chat_id)
        // ));
                
    

    } else if (strpos($text, "/eventinfo") === 0 || strpos($text, "/updateevent") === 0) {
        
        apiRequest("sendMessage", array('chat_id' => $chat_id, "text" =>  "$first_name! Below are the new event details 👏"));
      processEventInfoMessage($message);
      
    } else if (strpos($text, "/register") === 0) {
      processRegistration($message); // Start the registration process
    } else if (checkUserState($chat_id)) {
      continueRegistration($message); // Handle registration continuation
    } else { // if user entered something else
        
      apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Sorry! $first_name, I don't understand. Try to use bellow commands."));
      sendHelpMessage($chat_id,$first_name); // Call /help command function
      
    }
  } else {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
  }
}

function processCallbackQuery($callback_query) {
  $chat_id = $callback_query['message']['chat']['id'];
  $message = $callback_query['message'];
  $data = $callback_query['data'];
  $first_name = $callback_query['from']['first_name']; // Get the user's first name

  if ($data === 'start') {
    //startCommands($chat_id, $first_name);
    
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" =>  "Hello $first_name! How are you doing? 😄. Below are the new event details 👏"));
    // processEventInfoMessage($message);
    $eventDetails = getEventDetails();
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $eventDetails, "parse_mode" => "HTML"));
    //sendHelpMessage($chat_id, $first_name);
    
  } elseif ($data === 'help') {
      sendHelpMessage($chat_id, $first_name);
    // processMessage(array('message_id' => $callback_query['message']['message_id'], 'chat' => array('id' => $chat_id), 'text' => '/help'));
  } elseif ($data === 'register') {
    processMessage(array('message_id' => $callback_query['message']['message_id'], 'chat' => array('id' => $chat_id), 'text' => '/register'));
  } elseif ($data === 'eventInfo') {
    processMessage(array('message_id' => $callback_query['message']['message_id'], 'chat' => array('id' => $chat_id), 'text' => '/eventinfo'));
  }
}

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
} elseif (isset($update["callback_query"])) {
  processCallbackQuery($update["callback_query"]);
}

?>
