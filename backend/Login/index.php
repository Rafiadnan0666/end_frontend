<?php
header('Access-Control-Allow-Origin: http://localhost:3001');
header("Content-Type: application/json; charset=UTF-8");
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
include '../config.php';

$username = $_POST['username'];
$password = $_POST['password'];
$token = md5($username . $password);

$result = $connect->query("UPDATE user SET token = '$token' WHERE username = '$username' AND password = '$password'");

if ($result) {
    $code = 200;
    $message = 'Login Successful';
} else {
    $code = 404;
    $message = 'Login Failed';
}
$query = "SELECT * FROM user WHERE username = '$username'";
$oke = mysqli_query($connect, $query);
$jadi = mysqli_fetch_assoc($oke);
$response = [
    'code' => $code,
    'headers' => [
        'Access-Control-Allow-Origin' => '*'
    ],
    'message' => $jadi,
    'data' => $jadi
];

header('Content-Type: application/json');

foreach ($response['headers'] as $username => $value) {
    header("$username: $value");
}

echo json_encode($response, JSON_PRETTY_PRINT);
