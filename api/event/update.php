<?php
header("Content-Type: application/json");
require_once '../../entities/event.php';

$input = json_decode(file_get_contents("php://input"), true);

$data = $input['data'];
$conditions = $input['conditions'];

$event = new Event();
$success = $event->update($data, $conditions);
echo json_encode(["success" => $success]);
