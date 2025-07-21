<?php
class SizeModel{

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllSizes() {
    $query = "SELECT * FROM size";
    return $this->db->select($query);
}

}
