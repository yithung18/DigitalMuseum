<?php
$database = new SQLite3('museum.db');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['first-name']);
    $lastName = trim($_POST['last-name']);
    $fullName = $firstName . ' ' . $lastName;  // Combine first and last name
    $gender = $_POST['gender'] ?? '';
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm-password']);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        die("Passwords do not match. <a href='DMRegister.html'>Try again</a>");
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username already exists
    $checkUser = $database->prepare("SELECT id FROM users WHERE username = ?");
    $checkUser->bindValue(1, $username, SQLITE3_TEXT);
    $resultUser = $checkUser->execute();
    if ($resultUser->fetchArray()) {
        die("Username already exists. <a href='DMRegister.html'>Try again</a>");
    }

    // Check if the email already exists
    $checkEmail = $database->prepare("SELECT id FROM users WHERE email = ?");
    $checkEmail->bindValue(1, $email, SQLITE3_TEXT);
    $resultEmail = $checkEmail->execute();
    if ($resultEmail->fetchArray()) {
        die("Email already registered. <a href='DMRegister.html'>Try again</a>");
    }

    // Insert user into the database with default role as "member"
    $stmt = $database->prepare("INSERT INTO users (name, gender, username, email, password, role, mute) VALUES (?, ?, ?, ?, ?, 'member','0')");
    $stmt->bindValue(1, $fullName, SQLITE3_TEXT);
    $stmt->bindValue(2, $gender, SQLITE3_TEXT);
    $stmt->bindValue(3, $username, SQLITE3_TEXT);
    $stmt->bindValue(4, $email, SQLITE3_TEXT);
    $stmt->bindValue(5, $hashedPassword, SQLITE3_TEXT);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: DMLogin.html");
        exit();
    } else {
        echo "Error: Could not register.";
    }
}
?>
