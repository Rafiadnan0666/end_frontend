<?php
include "../config.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
function uploadFile($file, $targetDir)
{
    $fileName = basename($file["name"]);
    $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

    $uniqueFileName = uniqid() . '_' . $fileName;
    $targetFilePath = $targetDir . $uniqueFileName;

    while (file_exists($targetFilePath)) {
        $uniqueFileName = uniqid() . '_' . $fileName;
        $targetFilePath = $targetDir . $uniqueFileName;
    }

    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        return ["success" => true, "filePath" => $targetFilePath];
    } else {
        return ["success" => false, "message" => "File upload failed."];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $user_id = $_POST['user_id'];
    $price = $_POST['price'];
    $type = $_POST['type'];
    $details = $_POST['details'];
    $genre_id = $_POST['genre_id'];


    $file = null;
    if (isset($_FILES["file"])) {
        $fileUploadResult = uploadFile($_FILES["file"], "../file/");
        if (!$fileUploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $fileUploadResult["message"]]);
            exit();
        }
        $file = $fileUploadResult["filePath"];
    }


    $image1 = null;
    if (isset($_FILES["image1"])) {
        $image1UploadResult = uploadFile($_FILES["image1"], "../img/");
        if (!$image1UploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $image1UploadResult["message"]]);
            exit();
        }
        $image1 = $image1UploadResult["filePath"];
    }


    $banner = null;
    if (isset($_FILES["banner"])) {
        $bannerUploadResult = uploadFile($_FILES["banner"], "../img/");
        if (!$bannerUploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $bannerUploadResult["message"]]);
            exit();
        }
        $banner = $bannerUploadResult["filePath"];
    }


    $demo = null;
    if (isset($_FILES["demo"])) {
        $demoUploadResult = uploadFile($_FILES["demo"], "../img/");
        if (!$demoUploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $demoUploadResult["message"]]);
            exit();
        }
        $demo = $demoUploadResult["filePath"];
    }


    $image2 = null;
    if (isset($_FILES["image2"])) {
        $image2UploadResult = uploadFile($_FILES["image2"], "../img/");
        if (!$image2UploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $image2UploadResult["message"]]);
            exit();
        }
        $image2 = $image2UploadResult["filePath"];
    }

    $image3 = null;
    if (isset($_FILES["image3"])) {
        $image3UploadResult = uploadFile($_FILES["image3"], "../img/");
        if (!$image3UploadResult["success"]) {
            http_response_code(500);
            echo json_encode(["message" => $image3UploadResult["message"]]);
            exit();
        }
        $image3 = $image3UploadResult["filePath"];
    }

    $id = isset($_GET['id']) ? $_GET['id'] : null;

    if ($id) {
        $sql = "UPDATE gassets SET name = '$name', ";
        if ($file) {
            $sql .= "file = '$file', ";
        }
        if ($banner) {
            $sql .= "banner = '$banner', ";
        }
        if ($demo) {
            $sql .= "demo = '$demo', ";
        }
        if ($image1) {
            $sql .= "image1 = '$image1', ";
        }
        if ($image2) {
            $sql .= "image2 = '$image2', ";
        }
        if ($image3) {
            $sql .= "image3 = '$image3', ";
        }
        $sql .= "user_id = '$user_id', price = '$price', type = '$type', details = '$details', genre_id = '$genre_id' WHERE id = $id";
    } else {
        $sql = "INSERT INTO gassets (name, user_id, price, type, details, genre_id";
        if ($file) {
            $sql .= ", file";
        }
        if ($banner) {
            $sql .= ", banner";
        }
        if ($demo) {
            $sql .= ", demo";
        }
        if ($image1) {
            $sql .= ", image1";
        }
        if ($image2) {
            $sql .= ", image2";
        }
        if ($image3) {
            $sql .= ", image3";
        }
        $sql .= ") VALUES ('$name', '$user_id', '$price', '$type', '$details', '$genre_id'";
        if ($file) {
            $sql .= ", '$file'";
        }
        if ($banner) {
            $sql .= ", '$banner'";
        }
        if ($demo) {
            $sql .= ", '$demo'";
        }
        if ($image1) {
            $sql .= ", '$image1'";
        }
        if ($image2) {
            $sql .= ", '$image2'";
        }
        if ($image3) {
            $sql .= ", '$image3'";
        }
        $sql .= ")";
    }

    if ($connect->query($sql)) {
        http_response_code($id ? 200 : 201);
        echo json_encode(["message" => $id ? "User updated successfully" : "User created successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Failed to " . ($id ? "update" : "create") . " user"]);
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : null;
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id) {
        $result = $connect->query("SELECT * FROM gassets WHERE id = $id");
    } else if ($user_id) {
        $result = $connect->query("SELECT * FROM gassets WHERE user_id = $user_id");
    } else {
        $result = $connect->query("SELECT * FROM gassets");
    }
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }

    http_response_code(200);
    echo json_encode($users);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $delete_id = isset($_GET['delete_id']) ? intval($_GET['delete_id']) : null;

    if ($delete_id) {
        $name = $connect->query("SELECT * FROM gassets WHERE id = $delete_id");
        $sil = mysqli_fetch_assoc($name);

        $sql1 = "DELETE FROM komen WHERE game_id = $delete_id";
        $connect->query($sql1);

        $sql = "DELETE FROM gassets WHERE id = $delete_id";

        if ($connect->query($sql)) {
            $deleted_file = unlink($sil["file"]);
            $deleted_image = unlink($sil["image1"]);

            if ($deleted_file && $deleted_image) {
                http_response_code(200);
                echo json_encode(["message" => "Gasset deleted successfully."]);
            } else {
                http_response_code(200);
                echo json_encode(["message" => "Gasset deleted from database, but associated files might not have been deleted."]);
            }
        } else {
  
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete gasset."]);
        }
    }
}

