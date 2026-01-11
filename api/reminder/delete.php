<?php
header("Content-Type: application/json");
require_once '../../entities/reminder.php';

$input = json_decode(file_get_contents("php://input"), true);
$reminder = new Reminder();
echo json_encode([
    "success" => $reminder->delete($input['conditions'])
]);
