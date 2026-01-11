<?php
header("Content-Type: application/json");
require_once '../../entities/event.php';

$event = new Event();
$input = json_decode(file_get_contents("php://input"), true);
$result = $event->read($input['conditions']);
echo json_encode(["data" => $result]);
