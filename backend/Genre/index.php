<?php
include "../config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Function to handle genre addition
function addgenre($genre)
{
    global $connect;
    $sql = "INSERT INTO genre (genre) VALUES ('$genre')";
    if ($connect->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to handle genre update
function updategenre($id, $genre)
{
    global $connect;
    $sql = "UPDATE genre SET genre='$genre' WHERE id='$id'";
    if ($connect->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to retrieve all genres
function readgenres()
{
    global $connect;
    $sql = "SELECT * FROM genre";
    $result = $connect->query($sql);
    $genres = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

// Function to retrieve genres for a game
function getgenres($game_id)
{
    global $connect;
    $sql = "SELECT * FROM genre";
    $result = $connect->query($sql);
    $genres = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

function deletegenre($id)
{
    global $connect;
    $sql = "DELETE FROM genre WHERE id = '$id'";
    if ($connect->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (addgenre($data['genre'])) {
        http_response_code(201);
        echo json_encode(["message" => "genre added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to add genre"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $genres = readgenres();
    http_response_code(200);
    echo json_encode($genres);
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['id']) && isset($data['genre'])) {
        $id = $data['id'];
        $genre = $data['genre'];
        if (updategenre($id, $genre)) {
            http_response_code(200);
            echo json_encode(["message" => "genre updated successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to update genre"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid data provided"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['id'])) {
        $id = $data['id'];
        if (deletegenre($id)) {
            http_response_code(200);
            echo json_encode(["message" => "genre deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete genre"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid data provided"]);
    }
}
