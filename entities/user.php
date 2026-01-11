<?php
require_once __DIR__ . '/../core/operation.php';

class User {
    private $operation;
    private $table = "user";

    public function __construct() {
        $this->operation = new Operation();
    }

    public function signup($data) {
        // تحقق إذا كان الموبايل موجوداً بالفعل
        $existing = $this->operation->read($this->table, "phone = '{$data['phone']}'");
        if (!empty($existing)) {
            return ["success" => false, "message" => "phone number already exists"];
        }

        // تشفير كلمة المرور
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $response = $this->operation->createAndReturnId($this->table, $data);
        return $response;
    }

    public function login($phone, $password) {
        $user = $this->operation->read($this->table, "phone = '$phone'");
        if (empty($user)) {
            return ["success" => false, "message" => "User not found"];
        }

        $user = $user[0];
        if (password_verify($password, $user['password'])) {
            unset($user['password']);
            return ["success" => true, "data"=>["user" => $user]];
        } else {
            return ["success" => false, "message" => "Invalid password"];
        }
    }
    public function update($data, $conditions) {
        return $this->operation->update($this->table, $data, $conditions);
    }

}
