<?php
$database = new SQLite3('museum.db');

// Enable foreign key constraints
$database->exec("PRAGMA foreign_keys = ON;");

// Create "items" table if it doesn't exist
$database->exec("CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section TEXT,
    title TEXT,
    description TEXT,
    image TEXT
)");

// Create "users" table if it doesn't exist
$database->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    gender TEXT NOT NULL,
    username TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    role TEXT NOT NULL,
    mute INTEGER NOT NULL DEFAULT 0,
    profile_pic TEXT DEFAULT 'icon_profile.png'
)");

// Create "comments" table if it doesn't exist
$database->exec("CREATE TABLE IF NOT EXISTS comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    comment TEXT NOT NULL,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)");

// ✅ Create "favorites" table if it doesn't exist
$database->exec("CREATE TABLE IF NOT EXISTS favorites (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
)");

// Check if admin already exists
$checkAdmin = $database->querySingle("SELECT id FROM users WHERE role = 'admin'");

if (!$checkAdmin) {
    // Default admin credentials
    $adminName = "Admin";
    $adminGender = "male";
    $adminUsername = "admin";
    $adminEmail = "admin@gmail.com";
    $adminPassword = password_hash("admin123", PASSWORD_DEFAULT); // Hash the password
    $adminRole = "admin";

    // Insert the admin user
    $stmt = $database->prepare("
        INSERT INTO users (name, gender, username, email, password, role, mute, profile_pic) 
        VALUES (?, ?, ?, ?, ?, ?, 0, COALESCE(?, 'icon_profile.png'))
    ");
    $stmt->bindValue(1, $adminName, SQLITE3_TEXT);
    $stmt->bindValue(2, $adminGender, SQLITE3_TEXT);
    $stmt->bindValue(3, $adminUsername, SQLITE3_TEXT);
    $stmt->bindValue(4, $adminEmail, SQLITE3_TEXT);
    $stmt->bindValue(5, $adminPassword, SQLITE3_TEXT);
    $stmt->bindValue(6, $adminRole, SQLITE3_TEXT);
    $stmt->bindValue(7, NULL, SQLITE3_TEXT); // Ensure it uses default
    $stmt->execute();

    echo "Admin user created successfully.<br>";
}

echo "Database initialized successfully.";
?>
