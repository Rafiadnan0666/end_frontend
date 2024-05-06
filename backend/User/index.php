<?php
include "../config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

function uploadFile($file, $targetDir)
{

    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (file_exists(uniqid() . $targetFilePath)) {
        return ["success" => false, "message" => "File already exists"];
    }

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return ["success" => true, "filePath" => $targetFilePath];
    } else {
        return ["success" => false, "message" => "File upload failed"];
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : null;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Check if email already exists in the database
    $existingUserQuery = "SELECT id FROM user WHERE email = '$email' AND id != $id";
    $existingUserResult = $connect->query($existingUserQuery);
    if ($existingUserResult->num_rows > 0) {
        http_response_code(400);
        echo json_encode(["message" => "Email already exists"]);
        exit();
    }
    $bannerFilePath = null;
    if (isset($_FILES["banner"])) {
        $bannerUploadResult = uploadFile($_FILES["banner"], "../file/");
        if (!$bannerUploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $bannerUploadResult["message"]]);
            exit();
        }
        $bannerFilePath = $bannerUploadResult["filePath"];
    }

    $profileFilePath = null;
    if (isset($_FILES["profile"])) {
        $profileUploadResult = uploadFile($_FILES["profile"], "../file/");
        if (!$profileUploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $profileUploadResult["message"]]);
            exit();
        }
        $profileFilePath = $profileUploadResult["filePath"];
    }

    $sql = "UPDATE user SET username = '$username'";
    if ($bannerFilePath) {
        $sql .= ", banner = '$bannerFilePath'";
    }
    if ($profileFilePath) {
        $sql .= ", profile = '$profileFilePath'";
    }
    $sql .= " WHERE id = $id";

    if ($connect->query($sql) === TRUE) {
        http_response_code(200);
        echo json_encode(["message" => "User updated successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to update user"]);
    }
}





if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id) {
        $result = $connect->query("SELECT * FROM user WHERE id = $id");
    } else {
        $result = $connect->query("SELECT * FROM user");
    }

    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    http_response_code(200);
    echo json_encode($users);
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $id = $_GET['id'];

    $stmt = $connect->prepare("DELETE FROM user WHERE id = $id");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(["message" => "User deleted successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to delete user"]);
    }
}
