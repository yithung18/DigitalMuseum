<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT");
header("Content-Type: application/json");

$database = new SQLite3('museum.db');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $results = $database->query("SELECT * FROM items");
    $items = [];
    while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
        $items[] = $row;
    }
    echo json_encode($items);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $database->prepare("INSERT INTO items (section, title, description, image) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, $data['section']);
    $stmt->bindValue(2, $data['title']);
    $stmt->bindValue(3, $data['description']);
    $stmt->bindValue(4, $data['image']);
    $stmt->execute();
    echo json_encode(["message" => "Item added successfully"]);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['id'])) {
        echo json_encode(["error" => "No ID provided"]);
        exit;
    }

    $stmt = $database->prepare("UPDATE items SET section = ?, title = ?, description = ?, image = ? WHERE id = ?");
    $stmt->bindValue(1, $data['section']);
    $stmt->bindValue(2, $data['title']);
    $stmt->bindValue(3, $data['description']);
    $stmt->bindValue(4, $data['image']);
    $stmt->bindValue(5, $data['id']);
    $stmt->execute();

    echo json_encode(["message" => "Item updated successfully"]);
} elseif ($method === 'DELETE') {
    parse_str(file_get_contents("php://input"), $_DELETE);
    if (!isset($_DELETE['id'])) {
        echo json_encode(["error" => "No ID provided"]);
        exit;
    }

    $stmt = $database->prepare("DELETE FROM items WHERE id = ?");
    $stmt->bindValue(1, $_DELETE['id']);
    $stmt->execute();

    echo json_encode(["message" => "Item deleted successfully"]);
} else {
    echo json_encode(["message" => "Invalid request"]);
}
?>