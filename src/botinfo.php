<?php

$BOT_TOKEN = "7245509649:AAFmNUn07iZaMZfroczuoK6CKEV0ZR1kXW0";
$API_URL = "https://api.telegram.org/bot{$BOT_TOKEN}/getMe";

$response = file_get_contents($API_URL);

$Webhook_URL = "https://wijewardeneindustries.com/EventTicketingTelegramBot/main.php";
$webhook_api = "https://api.telegram.org/bot{$BOT_TOKEN}/setWebhook?url={$Webhook_URL}";
$webhook_response = file_get_contents($Webhook_URL);

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