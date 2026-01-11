<?php
header("Content-Type: application/json");

require_once '../../entities/user.php';
require_once '../../entities/patient.php';

// ✅ قراءة البيانات من POST (لأنه Multipart Form، وليس JSON)
$data = $_POST;

// ✅ التأكد من وجود الحقول المطلوبة
$requiredFields = ['name', 'phone', 'password', 'caregiver_id', 'age', 'gender', 'diagnosis_result'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required field: $field"
        ]);
        exit;
    }
}

// ✅ التأكد من رفع صورة MRI
if (!isset($_FILES['mri_image_path']) || $_FILES['mri_image_path']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "success" => false,
        "message" => "MRI image upload failed"
    ]);
    exit;
}

// ✅ رفع الصورة إلى مجلد محلي
$uploadDir = '../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // إنشاء المجلد إذا غير موجود
}

$image = $_FILES['mri_image_path'];
$uniqueName = uniqid('mri_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
$targetPath = $uploadDir . $uniqueName;

if (!move_uploaded_file($image['tmp_name'], $targetPath)) {
    echo json_encode([
        "success" => false,
        "message" => "Failed to save uploaded MRI image"
    ]);
    exit;
}

// ✅ إنشاء المستخدم
$user = new User();
$userData = [
    "name"     => $data["name"],
    "phone"    => $data["phone"],
    "password" => $data["password"],
    "role"     => "patient",
];

$userResult = $user->signup($userData);
if (!$userResult || !$userResult["success"]) {
    echo json_encode([
        "success" => false,
        "message" => $userResult["message"] ?? "Failed to create user"
    ]);
    exit;
}

// ✅ إنشاء المريض باستخدام user_id
$userId = $userResult['data']["id"];

$patient = new Patient();
$patientData = [
    "user_id"          => $userId,
    "caregiver_id"     => $data["caregiver_id"],
    "age"              => $data["age"],
    "gender"           => $data["gender"],
    "mri_image_path"   => $uniqueName, // فقط اسم الصورة المخزنة
    "diagnosis_result" => $data["diagnosis_result"],
];

$patientResponse = $patient->create($patientData);

// ✅ الرد النهائي
echo json_encode([
    "success"  => $patientResponse['success']
]);
