<?php

$BOT_TOKEN = "7245509649:AAFmNUn07iZaMZfroczuoK6CKEV0ZR1kXW0";
$Webhook_URL = "https://wijewardeneindustries.com/EventTicketingTelegramBot/index.php";
$webhook_api = "https://api.telegram.org/bot{$BOT_TOKEN}/setWebhook?url={$Webhook_URL}";
$webhook_response = file_get_contents($webhook_api);

$webhook_info_url = "https://api.telegram.org/bot{$BOT_TOKEN}/getWebhookInfo";
$webhook_info_response = file_get_contents($webhook_info_url);


echo $webhook_response;
echo "<br/>";
echo $webhook_info_response;

// https://api.telegram.org/bot7245509649:AAFmNUn07iZaMZfroczuoK6CKEV0ZR1kXW0/setWebhook?url=https://wijewardeneindustries.com/EventTicketingTelegramBot/index.php

?>
