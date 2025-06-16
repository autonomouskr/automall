<?php
require_once('wp-load.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_data = json_decode(file_get_contents('php://input'), true);
    $raw_password = $input_data['password'];
    $hashed_password = $input_data['hash'];

    if (wp_check_password($raw_password, $hashed_password)) {
        echo json_encode(['match' => true]);
    } else {
        echo json_encode(['match' => false]);
    }
}
?>