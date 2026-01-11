<?php
header("Content-Type: application/json");
require_once '../../entities/event.php';

$input = json_decode(file_get_contents("php://input"), true);

$conditions = $input['conditions'];

$event = new Event();
$success = $event->delete($conditions);
echo json_encode(["success" => $success]);
