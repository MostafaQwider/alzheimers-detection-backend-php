<?php
header("Content-Type: application/json");
require_once '../../entities/family_member.php';

$familyMember = new FamilyMember();
$input = json_decode(file_get_contents("php://input"), true);
$result = $familyMember->read($input['conditions']);
echo json_encode(["data" => $result]);
