<?php
include "../config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Function to handle comment submission
function addComment($game_id, $user_id, $komen)
{
    global $connect;
    $sql = "INSERT INTO komen (game_id, user_id, komen ) VALUES ('$game_id', '$user_id', '$komen')";
    if ($connect->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

// Function to retrieve comments for a game
function getComments($game_id)
{
    global $connect;
    $sql = "SELECT komen.*,username,profile FROM komen INNER JOIN user ON user.id = komen.user_id  WHERE game_id = '$game_id'";
    $result = $connect->query($sql);
    $comments = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }
    return $comments;
}

function deleteComment($id)
{
    global $connect;
    $sql = "DELETE FROM komen WHERE id = '$id'";
    if ($connect->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $game_id = intval($_GET['game_id']); 
    $user_id = intval($_POST['user_id']);
    $komen = $_POST['komen'];

    if (addComment($game_id, $user_id, $komen)) {
        http_response_code(201);
        echo json_encode(["message" => "Comment added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to add comment"]);
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['game_id'])) {
        $game_id = intval($_GET['game_id']);
        $comments = getComments($game_id);
        http_response_code(200);
        echo json_encode($comments);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Missing game_id parameter"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['id']){
    $id = intval($_GET['id']);
    if (deleteComment($id)) {
        http_response_code(200);
        echo json_encode(["message" => "Comment deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete comment"]);
    }
}
