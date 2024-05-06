<?php
include '../config.php';

$email = $_POST['email'];
$username = $_POST["username"];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if email already exists
$query = "SELECT COUNT(*) FROM user WHERE email = '$email'";
$result = mysqli_query($connect, $query);
$email_count = mysqli_fetch_assoc($result)['COUNT(*)'];

if ($email_count > 0) {
    $code = 400;
    $message = 'Email already exists';
} else {
    $token = password_hash($password . $username, PASSWORD_DEFAULT);

    $query = "INSERT INTO user (username, password, email, token) VALUES ('$username','$password','$email','$token')";
    mysqli_query($connect, $query);

    if (mysqli_affected_rows($connect) === 1) {
        $code = 200;
        $message = 'Registration Successful';
    } else {
        $code = 404;
        $message = 'Registration Failed';
    }
}

$query = "SELECT * FROM user WHERE email = '$email'";
$result = mysqli_query($connect, $query);
$jadi = mysqli_fetch_assoc($result);

echo json_encode([
    'Response' => $code,
    'Message' => $jadi,
    'data' => $jadi ?? null
]);
