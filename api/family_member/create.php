<?php
header("Content-Type: application/json");
require_once '../../entities/family_member.php';

// قراءة البيانات من POST
$data = $_POST;

// التأكد من وجود الحقول المطلوبة بدون photo لأن photo ممكن تكون اختيارية
$requiredFields = ['name', 'relation', 'phone', 'patient_id'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode([
            "success" => false,
            "message" => "Missing required field: $field"
        ]);
        exit;
    }
}

// رفع صورة photo إذا موجودة
$uploadDir = '../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

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
    // أضف اسم الصورة للبيانات المرسلة
    $data['photo'] = $uniqueName;
} else {
    // لو الصورة اختيارية ممكن تتجاهل، أو ترسل رسالة خطأ لو ضرورية
    // مثلا:
    // echo json_encode(["success" => false, "message" => "Photo upload failed or missing"]);
    // exit;
}

// إنشاء عضو العائلة
$familyMember = new FamilyMember();
$success = $familyMember->create($data);

echo json_encode([
    "success" => $success
]);
