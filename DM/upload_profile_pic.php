<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "Error: User not logged in.";
    exit;
}

if (!empty($_FILES['profile_pic']['name'])) {
    $upload_dir = "";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = basename($_FILES["profile_pic"]["name"]);
    $target_file = $upload_dir . $user_id . "_" . $file_name;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
        $stmt = $database->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bindValue(1, $target_file, SQLITE3_TEXT);
        $stmt->bindValue(2, $user_id, SQLITE3_INTEGER);
        $stmt->execute();
        echo "Profile picture updated.";
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}
?>
