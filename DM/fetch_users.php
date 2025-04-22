<?php
$database = new SQLite3('museum.db');

$results = $database->query("SELECT id, username, email, role, profile_pic FROM users");

$users = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $users[] = $row;
}

header('Content-Type: application/json');
echo json_encode($users);
?>
