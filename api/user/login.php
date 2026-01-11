<?php
header("Content-Type: application/json");
require_once '../../entities/user.php';

$data = json_decode(file_get_contents("php://input"), true);
$user = new User();
echo json_encode($user->login($data['phone'], $data['password']));
