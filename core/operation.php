<?php
require_once __DIR__ . '/../config/database.php';

class Operation {
    private $conn;

    public function __construct() {
        $db = new Database();
        if ($db->connect()) {
            $this->conn = $db->getConnection();
        }
    }

    public function create($table, $data) {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function createAndReturnId($table, $data) {
    $columns = implode(",", array_keys($data));
    $placeholders = ":" . implode(",:", array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

    $stmt = $this->conn->prepare($sql);
    $success = $stmt->execute($data);

    return [
        "success" => $success,
        "data"=>[
        "id" => $success ? $this->conn->lastInsertId() : null]
    ];
}


    public function read($table, $conditions = "") {
        $sql = "SELECT * FROM $table";
        if (!empty($conditions)) $sql .= " WHERE $conditions";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($table, $data, $conditions) {
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key = :$key, ";
        }
        $set = rtrim($set, ', ');

        $sql = "UPDATE $table SET $set WHERE $conditions";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($table, $conditions) {
        $sql = "DELETE FROM $table WHERE $conditions";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }

    public function readJoin(array $tables, array $onConditions, $whereConditions = "") {
    // تحقق من صحة عدد الجداول وشروط الربط
    if (count($tables) < 2 || count($onConditions) != count($tables) - 1) {
        throw new Exception("Invalid join parameters");
    }

    // بناء جملة SQL الأساسية
    $sql = "SELECT * FROM " . $tables[0];

    // بناء باقي جملة الـ INNER JOIN
    for ($i = 1; $i < count($tables); $i++) {
        $sql .= " INNER JOIN " . $tables[$i] . " ON " . $onConditions[$i - 1];
    }

    // إضافة شرط WHERE إن وجد
    if (!empty($whereConditions)) {
        $sql .= " WHERE $whereConditions";
    }

    // تنفيذ الاستعلام
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function loginPatient($phone, $password) {
    if (!$this->conn) {
        return ['success' => false, 'message' => 'فشل الاتصال بقاعدة البيانات'];
    }

    try {
        // 1. جلب المستخدم من جدول user
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE phone = :phone AND role = 'patient'");
        $stmt->execute(['phone' => $phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'رقم الهاتف غير موجود أو ليس مريضاً'];
        }

        // 2. التحقق من كلمة المرور
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'كلمة المرور غير صحيحة'];
        }

        // 3. جلب بيانات المريض المرتبطة بالمستخدم
        $patient = $this->read('patient', "user_id = " . $user['id']);
        if (empty($patient)) {
            return ['success' => false, 'message' => 'لم يتم العثور على بيانات المريض'];
        }

        $patientData = $patient[0];

        // 4. جلب بيانات المستخدم المشرف caregiver
        $caregiver = $this->read('user', "id = " . $patientData['caregiver_id']);
        if (empty($caregiver)) {
            return ['success' => false, 'message' => 'لم يتم العثور على بيانات المشرف'];
        }

        // 5. إرجاع البيانات
        return [
            'success' => true,
            'data' => [
                'user' => $user,
                'patient' => $patientData,
                'caregiver' => $caregiver[0]
            ]
        ];

    } catch (Exception $e) {
        return ['success' => false, 'message' => 'خطأ داخلي', 'error' => $e->getMessage()];
    }
}



}
