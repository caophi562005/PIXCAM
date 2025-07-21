<?php
class SubCategoryModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAll() {
        $query = "SELECT id, name FROM subcategory";
        $result = $this->db->select($query);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
