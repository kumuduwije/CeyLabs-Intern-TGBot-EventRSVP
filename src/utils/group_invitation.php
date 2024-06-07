<?php

// Ensure the path points to the correct location of config.json
$config = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);

if ($config === null) {
    throw new Exception('Unable to load configuration file.');
}

define('GROUP_INVITE_LINK', $config['group_invite_link']); // Group invite link from config.json
function inviteUserToGroup($chat_id) {
    apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "You have been added to the group: " . GROUP_INVITE_LINK));
}

?>
