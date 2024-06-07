<?php

define('DEFAULT_DATABASE_FILE', __DIR__ . '/../database.json');

// Load data from the JSON file
function loadDatabase($databaseFile = DEFAULT_DATABASE_FILE) {
    if (!file_exists($databaseFile)) {
        return array();
    }
    $json = file_get_contents($databaseFile);
    return json_decode($json, true);
}

// Save data to the JSON file
function saveDatabase($data, $databaseFile = DEFAULT_DATABASE_FILE) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($databaseFile, $json);
}

// Save user information and ticket details
function saveUserInfo($userInfo, $databaseFile = DEFAULT_DATABASE_FILE) {
    $data = loadDatabase($databaseFile);
    $data[] = $userInfo;
    saveDatabase($data, $databaseFile);
}

?>
