<?php
header("Content-Type: application/json");
require_once '../../entities/family_member.php';

$input = json_decode(file_get_contents("php://input"), true);

$conditions = $input['conditions'];

$familyMember = new FamilyMember();
$success = $familyMember->delete($conditions);
echo json_encode(["success" => $success]);
