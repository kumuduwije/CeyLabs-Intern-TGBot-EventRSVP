<?php


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
define('API_URL', 'https://api.telegram.org/bot'.$config['bot_token'].'/getme');
define("WEBHOOK_URL", $config['webhook_url']);


$response = file_get_contents(API_URL);

$webhook_api = "https://api.telegram.org/bot".BOT_TOKEN."/setWebhook?url=".WEBHOOK_URL;
$webhook_response = file_get_contents(WEBHOOK_URL);
echo $webhook_response;


if($response !== false) {
    $data = json_decode($response, true);

    if($data["ok"] != false) {
        echo "<br/> \n\n";
        echo "Bot username:{$data['result']['username']}"; 
        echo "<br/> \n";
        echo "Bot id:{$data['result']['id']}"; 

    } else {
        echo "Error getting bot information.";
    }
}else{
    echo "Error getting bot information.";
    
}