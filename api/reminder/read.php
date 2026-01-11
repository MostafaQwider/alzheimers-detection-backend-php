<?php
header("Content-Type: application/json");
require_once '../../entities/reminder.php';

$reminder = new Reminder();
$input = json_decode(file_get_contents("php://input"), true);
echo json_encode(["data" => $reminder->read($input['conditions'])]);
