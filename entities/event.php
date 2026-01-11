<?php
require_once __DIR__ . '/../core/operation.php';

class Event {
    private $operation;
    private $table = "event";

    public function __construct() {
        $this->operation = new Operation();
    }

    public function create($data) {
        return $this->operation->create($this->table, $data);
    }

    public function read($conditions = "") {
        return $this->operation->read($this->table, $conditions);
    }

    public function update($data, $conditions) {
        return $this->operation->update($this->table, $data, $conditions);
    }

    public function delete($conditions) {
        return $this->operation->delete($this->table, $conditions);
    }
}
