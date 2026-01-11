<?php
header("Content-Type: application/json");
require_once '../../entities/patient.php';

$patient = new Patient();

// قراءة معرف مقدم الرعاية من الـ GET
$caregiverId = isset($_GET['caregiver_id']) ? $_GET['caregiver_id'] : null;

if ($caregiverId === null) {
    echo json_encode(["error" => "Missing caregiver_id"]);
    exit;
}

// جلب البيانات مع join من الجدولين
$result = $patient->readWithUserInfo($caregiverId);

// إرجاع البيانات على شكل JSON
echo json_encode(["data" => $result]);
