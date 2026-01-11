<?php
header("Content-Type: application/json");
require_once '../../core/operation.php';

$data = json_decode(file_get_contents("php://input"), true);

$operation = new Operation();
$response = $operation->loginPatient($data['phone'], $data['password']);

echo json_encode($response);
