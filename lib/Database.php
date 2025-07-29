<?php
require_once __DIR__ . '/../config/config.php';
class Database {
    public $host   = DB_HOST;
    public $user   = DB_USER;
    public $pass   = DB_PASS;
    public $dbname = DB_NAME;
    public $dbport = DB_PORT;

    public $link;
    public $error;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $this->link = new mysqli($this->host, $this->user, $this->pass, $this->dbname, $this->dbport);
        if (!$this->link) {
            $this->error = "Connection fail: " . $this->link->connect_error;
            return false;
        }
    }

    // Select / Read data
    public function select($query) {
        $result = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($result->num_rows > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // Insert data
    public function insert($query) {
        $insert_row = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($insert_row) {
            return $insert_row;
        } else {
            return false;
        }
    }

    // Update data
    public function update($query) {
        $update_row = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($update_row) {
            return $update_row;
        } else {
            return false;
        }
    }

    // Delete data
    public function delete($query) {
        $delete_row = $this->link->query($query) or die($this->link->error . __LINE__);
        if ($delete_row) {
            return $delete_row;
        } else {
            return false;
        }
    }
    public function prepareSelect($query, $params = [], $types = "") {
        $stmt = $this->prepareAndExecute($query, $params, $types);
        $result = $stmt->get_result();
        return $result->num_rows > 0 ? $result : false;
    }

    // INSERT - dùng prepare()
    public function prepareInsert($query, $params = [], $types = "") {
    $stmt = $this->prepareAndExecute($query, $params, $types);

    if ($stmt->error) {
        die("Insert failed: " . $stmt->error);
    }

    $result = $stmt->affected_rows > 0;
    $stmt->close();
    return $result;
}

    // UPDATE - dùng prepare()
    public function prepareUpdate($query, $params = [], $types = "") {
        $stmt = $this->prepareAndExecute($query, $params, $types);
        return $stmt->affected_rows > 0;
    }

    // DELETE - dùng prepare()
    public function prepareDelete($query, $params = [], $types = "") {
        $stmt = $this->prepareAndExecute($query, $params, $types);
        return $stmt->affected_rows > 0;
    }

    // Hàm nội bộ để chuẩn bị và thực thi
    private function prepareAndExecute($query, $params, $types) {
        $stmt = $this->link->prepare($query);
        if (!$stmt) {
            die("Prepare failed: " . $this->link->error);
        }

        if (!empty($params)) {
            // Dùng ... để unpack tham số khi bind_param
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }
    public function getInsertId() {
    return $this->link->insert_id;
}
   public function fetchOne($query, $params = [], $types = ""): ?array {
    $stmt = $this->prepareAndExecute($query, $params, $types);
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
}
?>