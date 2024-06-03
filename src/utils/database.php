<?php

define('DATABASE_FILE', 'database.json');

// Load data from the JSON file
function loadDatabase() {
    if (!file_exists(DATABASE_FILE)) {
        return array();
    }
    $json = file_get_contents(DATABASE_FILE);
    return json_decode($json, true);
}

// Save data to the JSON file
function saveDatabase($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(DATABASE_FILE, $json);
}

// Save user information and ticket details
function saveUserInfo($userInfo) {
    $data = loadDatabase();
    $data[] = $userInfo;
    saveDatabase($data);
}

?>
