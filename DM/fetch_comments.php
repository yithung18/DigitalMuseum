<?php
session_start();
$database = new SQLite3('museum.db');

$item_id = $_GET['item_id'] ?? 0;
$logged_in_user_role = $_SESSION['role'] ?? 'user'; // Get role from session

$stmt = $database->prepare("
    SELECT comments.id AS comment_id, users.id AS user_id, users.profile_pic, users.username, users.mute, comments.comment, comments.timestamp 
    FROM comments 
    JOIN users ON comments.user_id = users.id 
    WHERE comments.item_id = :item_id 
    ORDER BY comments.timestamp DESC
");
$stmt->bindValue(':item_id', $item_id, SQLITE3_INTEGER);
$results = $stmt->execute();

while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $profilePicPath = "" . htmlspecialchars($row['profile_pic'] ?? 'default_profile.jpg');
    $isMuted = $row['mute'] == 1;
    
    echo '<div class="comment-container">';
    echo '<img src="' . $profilePicPath . '" class="comment-avatar" alt="User Avatar">';
    echo '<div class="comment-text">';
    echo '<strong>' . htmlspecialchars($row['username']) . '</strong>';
    
    // Show MUTE icon if user is muted
    if ($isMuted) {
        echo ' <span style="color: red;">(Muted)</span>';
    }
    
    echo '<br>' . htmlspecialchars($row['comment']);
    echo '<br><small>' . $row['timestamp'] . '</small>';

    // Show DELETE & MUTE buttons for admin
    if ($logged_in_user_role === 'admin') {
        echo '<br><button onclick="deleteComment(' . $row['comment_id'] . ')">Delete</button>';
        if ($isMuted) {
            echo '<button onclick="toggleMute(' . $row['user_id'] . ', 0)">Unmute</button>';
        } else {
            echo '<button onclick="toggleMute(' . $row['user_id'] . ', 1)">Mute</button>';
        }
    }
	if ($logged_in_user_role === 'staff') {
        echo '<br><button onclick="deleteComment(' . $row['comment_id'] . ')">Delete</button>';
        if ($isMuted) {
            echo '<button onclick="toggleMute(' . $row['user_id'] . ', 0)">Unmute</button>';
        } else {
            echo '<button onclick="toggleMute(' . $row['user_id'] . ', 1)">Mute</button>';
        }
    }
    echo '</div></div>';
}
?>
