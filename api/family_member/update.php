
<?php
header("Content-Type: application/json");
require_once '../../entities/family_member.php';

// ✅ استخدم $_REQUEST بدلاً من $_POST لقبول multipart/form-data
$data = $_REQUEST;

$requiredFields = ['id', 'name', 'relation', 'phone', 'patient_id'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required field: $field"
        ]);
        exit;
    }
}

// ✅ تجهيز مجلد الصور
$uploadDir = '../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ✅ رفع صورة جديدة إذا موجودة
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    $uniqueName = uniqid('photo_', true) . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
    $targetPath = $uploadDir . $uniqueName;

    if (!move_uploaded_file($photo['tmp_name'], $targetPath)) {
        echo json_encode([
            "success" => false,
            "message" => "Failed to save uploaded photo"
        ]);
        exit;
    }

    $data['photo'] = $uniqueName;
}

$familyMember = new FamilyMember();
$conditions = "id = " . intval($data['id']);
$success = $familyMember->update($data, $conditions);

echo json_encode([
    "success" => $success
]);
