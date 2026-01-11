<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method Not Allowed"]);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["message" => "No valid image uploaded"]);
    exit;
}

// مسار الصورة المؤقتة
$tmpName = $_FILES['image']['tmp_name'];
$fileName = $_FILES['image']['name'];
$fileType = $_FILES['image']['type'];

// عنوان API فلاسك (غيره حسب سيرفرك)
$flaskUrl = "http://127.0.0.1:5000/predict"; // أو http://IP:5000/predict

// إعداد cURL لارسال الصورة لـ Flask
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $flaskUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

// إرسال ملف الصورة
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'image' => new CURLFile($tmpName, $fileType, $fileName)
]);

// تنفيذ الطلب واستلام الرد
$response = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500);
    echo json_encode(["message" => "Error sending request to Flask API", "error" => curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// إعادة النتيجة كما هي من Flask (JSON)
echo $response;
