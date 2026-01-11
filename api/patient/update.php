<?php
header("Content-Type: application/json");
require_once '../../entities/user.php';
require_once '../../entities/patient.php';

$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON input"
    ]);
    exit;
}

if (empty($input['user_id']) || empty($input['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing user_id or id"
    ]);
    exit;
}

$userData = [
    'name'  => $input['name'] ?? null,
    'phone' => $input['phone'] ?? null,
    // تم حذف 'password' هنا
];
$userData = array_filter($userData, fn($v) => $v !== null);

$userId = intval($input['user_id']);

$patientData = [
    'age'    => $input['age'] ?? null,
    'gender' => $input['gender'] ?? null,
];
$patientData = array_filter($patientData, fn($v) => $v !== null);

$patientId = intval($input['id']);

$user = new User();
$patient = new Patient();

try {
    $userCondition = "id=$userId";
    $patientCondition = "id=$patientId";

    $userUpdated = true;
    $patientUpdated = true;

    if (!empty($userData)) {
        $userUpdated = $user->update($userData, $userCondition);
    }

    if (!empty($patientData)) {
        unset($patientData['mri_image_path'], $patientData['diagnosis_result']);
        $patientUpdated = $patient->update($patientData, $patientCondition);
    }

    if ($userUpdated && $patientUpdated) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update user or patient"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Exception: " . $e->getMessage()
    ]);
}
