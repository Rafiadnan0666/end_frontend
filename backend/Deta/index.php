<?php

require "../config.php";

$id = isset($_GET['id']) ? $_GET['id'] : null;
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($id) {
        $result = $connect->query("SELECT gassets.*, komen.komen FROM gassets INNER JOIN komen ON komen.game_id = gassets.id WHERE gassets.name = $id");
    } else {
        $result = $connect->query("SELECT gassets.*, komen.komen FROM gassets INNER JOIN komen ON komen.game_id = gassets.id ");
    }
    $gassets = [];
    while ($row = $result->fetch_assoc()) {
        $gassets[] = $row;
    }

    http_response_code(200);
    echo json_encode($gassets);
}
