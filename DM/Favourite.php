<?php
session_start();
$database = new SQLite3('museum.db');

$user_id = $_SESSION['user_id'] ?? 0;
if ($user_id == 0) {
    echo "<h2>Please log in to see your favorite items.</h2>";
    exit;
}

// Fetch user's favorite items
$results = $database->prepare("
    SELECT items.* FROM items
    JOIN favorites ON items.id = favorites.item_id
    WHERE favorites.user_id = ?
");
$results->bindValue(1, $user_id, SQLITE3_INTEGER);
$results = $results->execute();

$favorites = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $favorites[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favourite</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        .header {
            background-color: #d0c4aa;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .search-bar {
            display: flex;
            align-items: center;
        }
        .search-bar input[type="text"] {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-bar button {
            margin-left: 5px;
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            background-color: #f4f4f4;
            cursor: pointer;
        }
        .icon {
            width: 30px;
            height: 30px;
            cursor: pointer;
        }
        .side-menu {
            position: fixed;
            top: 0;
            right: -250px;
            width: 250px;
            height: 100%;
            background-color: #c8bca4;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.3);
            transition: right 0.3s ease-in-out;
            padding-top: 20px;
            z-index: 3;
        }
        .side-menu a {
            display: block;
            padding: 10px 20px;
            font-size: 18px;
            text-decoration: none;
            color: black;
        }
        .side-menu a:hover {
            background-color: #b0a58a;
        }
        .close-btn {
            font-size: 24px;
            cursor: pointer;
            padding: 10px;
            display: block;
            text-align: right;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }
        h1 {
            text-align: left;
            font-size: 24px;
            margin-left: 20px;
            margin-bottom: 20px;
        }
        .favourite-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            background-color: white;
            text-align: center;
            position: relative;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .card h3 {
            font-size: 18px;
            margin: 10px 0;
        }
        .card p {
            font-size: 14px;
            color: #555;
        }
        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
        }
        .favorite-btn:hover {
            color: red;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination button {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            background-color: #d0c4aa;
            cursor: pointer;
            font-size: 14px;
            border-radius: 5px;
        }
        .pagination button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="search-bar">
            <input type="text" placeholder="Search">
            <button>X</button>
        </div>
        <div>
            
            <span onclick="openMenu()" ><img src="icon_menu-bar.png" class="icon" > </span>
        </div>
    </div>
    <div id="side-menu" class="side-menu">
        <span class="close-btn" onclick="closeMenu()">✖</span>
        <a href="MainMenu.php">Home</a>
		<a href="Favourite.php">Favourite</a>
        <a href="HistoryOfCom.html">History of communication</a>
        <a href="Profile.html">Profile</a>
        <a href="AboutUs.html">About Us</a>
        <a href="ContactUs.html">Contact Us</a>
		<a href="DMLogin.htm">Log Out</a>
    </div>

    <h1>Favourite</h1>

    <div class="favourite-grid">
        <?php foreach ($favorites as $row): ?>
            <div class="card">
                <button class="favorite-btn" onclick="removeFavourite(<?= $row['id'] ?>)">⭐</button>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="Item Image">
                <p><?= htmlspecialchars($row['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        function removeFavourite(itemId) {
            fetch("toggle_favorite.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "item_id=" + itemId
            })
            .then(response => response.json())
            .then(data => {
                alert("Item removed from favorites.");
                location.reload();
            })
            .catch(error => console.error("Error:", error));
        }

        function openMenu() {
            document.getElementById("side-menu").style.right = "0";
        }
        
        function closeMenu() {
            document.getElementById("side-menu").style.right = "-250px";
        }
    </script>
</body>
</html>
