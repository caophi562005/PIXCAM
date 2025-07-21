<?php

require_once __DIR__ . '/../lib/Database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getProductsBySubCategory($subCategoryId) {
        $subCategoryId = intval($subCategoryId);
        $sql = "
            SELECT p.*, s.name AS subcategory_name, sa.name AS sale_name
            FROM product p
            INNER JOIN subcategory s ON p.subCategory_id = s.id
            LEFT JOIN sale sa ON p.Sale_id = sa.id
            WHERE s.id = $subCategoryId
            
        ";
        return $this->db->select($sql);
    }

    public function getProductsByCategory($categoryId) {
        $categoryId = intval($categoryId);
        $sql = "
            SELECT p.*
            FROM product p
            INNER JOIN subcategory s ON p.subCategory_id = s.id
            WHERE s.category_id = $categoryId
             
        ";
        return $this->db->select($sql);
    }
 public function getProductById($productId) {
        $productId = intval($productId);

        $sql = "
            SELECT 
                p.*,
                s.name AS subcategory_name,
                sa.name AS sale_name
            FROM product p
            INNER JOIN subcategory s ON p.subCategory_id = s.id
            LEFT JOIN sale sa ON p.Sale_id = sa.id
            WHERE p.id = $productId
            
        ";
        $result = $this->db->select($sql);
        if (!$result || $result->num_rows == 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        $productId = intval($row['id']);

        // Lấy danh sách size
        $sqlSizes = "
            SELECT sz.id, sz.name
            FROM productsize ps
            INNER JOIN size sz ON ps.size_id = sz.id
            WHERE ps.product_id = $productId
        ";
        $sizesResult = $this->db->select($sqlSizes);
        $sizes = [];
        if ($sizesResult) {
            while ($r = $sizesResult->fetch_assoc()) {
                $sizes[] = [
                    'id'   => intval($r['id']),
                    'name' => $r['name']
                ];
            }
        }
        $row['sizes'] = $sizes;

        // Lấy danh sách màu
        $sqlColors = "
            SELECT colorName
            FROM productcolor
            WHERE product_id = $productId
        ";
        $colorsResult = $this->db->select($sqlColors);
        $colors = [];
        if ($colorsResult) {
            while ($r = $colorsResult->fetch_assoc()) {
                $colors[] = $r['colorName'];
            }
        }
        $row['colors'] = $colors;

        // Tính giá sale nếu sale_name tồn tại và có dạng "xx%"
        if (!empty($row['sale_name'])) {
            // Loại bỏ hết ký tự không phải số, chỉ giữ lại phần số
            $percentStr = preg_replace('/\D/', '', $row['sale_name']); // ví dụ "30" từ "30%"
            $percent = intval($percentStr);
            if ($percent > 0 && $percent <= 100) {
                $priceOrig = floatval($row['price']);
                // Tính giá giảm
                $row['price_sale'] = $priceOrig * (100 - $percent) / 100;
                // Lưu thêm biến discount_percent để dễ gọi ở ngoài view nếu muốn
                $row['discount_percent'] = $percent;
            }
        }

        return $row;
    }

    /**
     * Lấy 4 sản phẩm ngẫu nhiên trong cùng subcategory (loại trừ sản phẩm hiện tại).
     * Tương tự, nếu có sale_name dạng "xx%", sẽ tính price_sale tại đây.
     */
    public function getRelatedProducts($subCategoryId, $excludeProductId, $limit = 4) {
        $subCategoryId    = intval($subCategoryId);
        $excludeProductId = intval($excludeProductId);
        $limit            = intval($limit);

        $sql = "
            SELECT 
                p.*,
                sa.name AS sale_name
            FROM product p
            LEFT JOIN sale sa ON p.Sale_id = sa.id
            WHERE p.subCategory_id = $subCategoryId
              AND p.id != $excludeProductId
          
            ORDER BY RAND()
            LIMIT $limit
        ";
        $result = $this->db->select($sql);
        $related = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Nếu có sale_name, parse để tính price_sale
                if (!empty($row['sale_name'])) {
                    $percentStr = preg_replace('/\D/', '', $row['sale_name']);
                    $percent = intval($percentStr);
                    if ($percent > 0 && $percent <= 100) {
                        $priceOrig = floatval($row['price']);
                        $row['price_sale'] = $priceOrig * (100 - $percent) / 100;
                        $row['discount_percent'] = $percent;
                    }
                }
                $related[] = $row;
            }
        }
        return $related;
    }

     public function getNewestProducts($limit = 12) {
        $limit = intval($limit);
        $sql = "
            SELECT 
                p.*,
                sa.name AS sale_name
            FROM product p
            LEFT JOIN sale sa ON p.Sale_id = sa.id
            
            ORDER BY p.id DESC
            LIMIT $limit
        ";
        $result = $this->db->select($sql);
        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Nếu có sale_name ở dạng "xx%", parse ra percent và tính price_sale
                if (!empty($row['sale_name']) && preg_match('/(\d+)%/', $row['sale_name'], $matches)) {
                    $percent = intval($matches[1]);
                    if ($percent > 0 && $percent <= 100) {
                        $priceOrig = floatval($row['price']);
                        $row['price_sale'] = $priceOrig * (100 - $percent) / 100;
                        $row['discount_percent'] = $percent;
                    }
                }
                $products[] = $row;
            }
        }
        return $products;
    }
   public function findProductsByName(string $keyword): array {
        // 1. Ép về chuỗi an toàn trước khi nhúng vào SQL
        $conn   = $this->db->link;  // chính là mysqli connection
        $clean  = mysqli_real_escape_string($conn, trim($keyword));

        // 2. Xây dựng câu query: LOWER(name) LIKE LOWER('%...%') để không phân biệt hoa thường
     $sql = "
    SELECT
        p.*,
        sa.name AS sale_name
    FROM product p
    LEFT JOIN sale sa ON p.Sale_id = sa.id
    WHERE LOWER(p.name) LIKE LOWER('%{$clean}%')
    ORDER BY p.id DESC
";

        // 3. Thực thi query
        $result = $this->db->select($sql);
        $products = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                // Nếu có sale_name ở dạng "xx%", tính giá sale
                if (!empty($row['sale_name']) && preg_match('/(\d+)%/', $row['sale_name'], $m)) {
                    $percent = intval($m[1]);
                    if ($percent > 0 && $percent <= 100) {
                        $priceOrig = floatval($row['price']);
                        $row['price_sale']        = $priceOrig * (100 - $percent) / 100;
                        $row['discount_percent']  = $percent;
                    }
                }
                $products[] = $row;
            }
        }

        return $products;
    }
    // Lấy tất cả sản phẩm
    public function getAllProducts() {
        $query = "SELECT * FROM product";
        return $this->db->select($query);
    }

    
        public function getProductByIdforUpdate($id) {
            $query = "SELECT * FROM product WHERE id = ?";
            $result = $this->db->prepareSelect($query, [$id], 'i');
            return $result ? $result->fetch_assoc() : null;
        }
    
    // Thêm sản phẩm mới
    public function addProduct($data) {
        $query = "INSERT INTO product (name, price, quantity, detail, imgURL_1, imgURL_2, imgURL_3, imgURL_4, subCategory_id, Sale_id)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $params = [
            $data['name'], $data['price'], $data['quantity'], $data['detail'],
            $data['imgURL_1'], $data['imgURL_2'], $data['imgURL_3'], $data['imgURL_4'],
            $data['subCategory_id'], $data['Sale_id']
        ];

        $types = "siisssssii"; // s: string, d: double, i: integer

        $success = $this->db->prepareInsert($query, $params, $types);
        return $success ? $this->db->getInsertId() : false;
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $data) {
        $query = "UPDATE product SET name = ?, price = ?, quantity = ?, detail = ?, imgURL_1 = ?, imgURL_2 = ?, imgURL_3 = ?, imgURL_4 = ?, updateAt = ?, subCategory_id = ?, Sale_id = ?
                  WHERE id = ?";

        $params = [
            $data['name'], $data['price'], $data['quantity'], $data['detail'],
            $data['imgURL_1'], $data['imgURL_2'], $data['imgURL_3'], $data['imgURL_4'],
            $data['updateAt'], $data['subCategory_id'], $data['Sale_id'], $id
        ];

        $types = "siisssssssii";

        return $this->db->prepareUpdate($query, $params, $types);
    }

    // Xóa sản phẩm
    public function deleteProduct($id) {
        $query = "DELETE FROM product WHERE id = ?";

        $this->db->prepareDelete("DELETE FROM cart WHERE product_id = ?", [$id], 'i');

        $this->db->prepareDelete("DELETE FROM productcolor WHERE product_id = ?", [$id], 'i');

        $this->db->prepareDelete("DELETE FROM productsize WHERE product_id = ?", [$id], 'i');

        return $this->db->prepareDelete($query, [$id], 'i');
    }
    public function addColor($productId, $colorName) {
        $query = "INSERT INTO productcolor (product_id, colorName) VALUES (?, ?)";
        return $this->db->prepareInsert($query, [$productId,$colorName], 'is');
    }
    public function addSize($productId, $sizeId) {
        $query = "INSERT INTO productsize (product_id, size_id) VALUES (?, ?)";
        return $this->db->prepareInsert($query, [$productId,$sizeId], 'ii');
    }

    public function getSizeByProductId($id){
        $query = "SELECT size_id FROM productsize WHERE product_id = ?";
        return $this->db->prepareSelect($query,[$id],'i');
    }

    public function getColorByProductId($id){
        $query = "SELECT colorName FROM productcolor WHERE product_id = ?";
        return $this->db->prepareSelect($query,[$id],'i');
    }

    public function deleteSizesByProductId($id) {
    $query = "DELETE FROM productsize WHERE product_id = ?";
    return $this->db->prepareDelete($query, [$id], "i");
}

public function deleteColorsByProductId($id) {
    $query = "DELETE FROM productcolor WHERE product_id = ?";
    return $this->db->prepareDelete($query, [$id], "i");
}
 // Đếm tổng số sản phẩm
    public function countAllProducts(): int {
        $sql = "SELECT COUNT(*) AS total FROM product";
        $row = $this->db->fetchOne($sql);
        return $row ? (int)$row['total'] : 0;
    }

    // Lấy danh sách sản phẩm theo limit/offset
    public function getProductsByPage(int $limit, int $offset): array {
        $sql = "SELECT * FROM product ORDER BY id DESC LIMIT ? OFFSET ?";
        $result = $this->db->prepareSelect($sql, [$limit, $offset], "ii");
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}