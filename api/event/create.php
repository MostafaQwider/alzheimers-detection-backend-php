<?php
header("Content-Type: application/json");
require_once '../../entities/event.php';

$data = json_decode(file_get_contents("php://input"), true);

$event = new Event();
$success = $event->create($data);
echo json_encode(["success" => $success]);
