<?php
header("Content-Type: application/json");
require_once '../../entities/patient.php';

// ✅ قراءة body بصيغة JSON
$input = json_decode(file_get_contents("php://input"), true);
$id = $input['id'] ?? null;

// ✅ التحقق من وجود id
if (!$id) {
    echo json_encode(["success" => false, "message" => "Missing id"]);
    exit;
}

// ✅ تنفيذ الحذف
$conditions = 'id=' . $id;

$patient = new Patient();
$success = $patient->delete($conditions);

// ✅ الرد النهائي
echo json_encode(["success" => $success]);
