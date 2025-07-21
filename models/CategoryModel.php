<?php
require_once 'lib/database.php';

class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy tất cả category (cha) từ bảng category
     */
    public function getAllCategories() {
        $query = "SELECT * FROM category";
        return $this->db->select($query);
    }

    /**
     * Lấy tất cả subcategory theo category_id
     */
    public function getSubCategoriesByCategoryId($categoryId) {
        $categoryId = intval($categoryId);
        $query = "SELECT * FROM subcategory WHERE category_id = $categoryId";
        return $this->db->select($query);
    }
}
?>