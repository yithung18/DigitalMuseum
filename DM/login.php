<?php
session_start();
$database = new SQLite3('museum.db');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the user exists
    $stmt = $database->prepare("SELECT id, name, password, role, mute FROM users WHERE username = ?");
    $stmt->bindValue(1, $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['mute'] = $user['mute']; // Store mute status in session

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: MainMenu_Admin.php");
            } elseif ($user['role'] === 'staff') {
				header("Location: MainMenu_Staff.php");
			}else {
                header("Location: MainMenu.php");
            }
            exit();
        } else {
            echo "Incorrect password. <a href='DMLogin.html'>Try again</a>";
        }
    } else {
        echo "User not found. <a href='DMLogin.html'>Try again</a>";
    }
}
?>
