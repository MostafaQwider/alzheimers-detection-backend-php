<?php
require_once __DIR__ . '/../core/operation.php';

class Patient {
    private $operation;
    private $table = "patient";
    
    public function __construct() {
        $this->operation = new Operation();
    }

    public function create($data) {
        return $this->operation->createAndReturnId($this->table, $data);
    }

    public function read($conditions = "") {
        return $this->operation->read($this->table, $conditions);
    }

    public function update($data, $conditions) {
        return $this->operation->update($this->table, $data, $conditions);
    }

    public function delete($conditions) {
        return $this->operation->delete("user", $conditions);
    }

    public function readWithUserInfo($caregiverId) {
    $tables = ['user', 'patient'];
    $onConditions = ['user.id = patient.user_id'];
    $where = "patient.caregiver_id = $caregiverId";

    return $this->operation->readJoin($tables, $onConditions, $where);
}
}
