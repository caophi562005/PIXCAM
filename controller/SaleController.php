<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../lib/Database.php';

class SaleController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->db = new Database();
    }

    public function index() {
        $productsArr = [];

        // ✅ Lấy theo sub_id (nếu có)
        if (isset($_GET['sub_id'])) {
    $subId = intval($_GET['sub_id']);
    $res = $this->productModel->getProductsBySubCategory($subId);
    $productsArr = [];
    if ($res) {
        while ($p = $res->fetch_assoc()) {
            $productsArr[] = $p;
        }
    }
} elseif (isset($_GET['cat_id'])) {
            $catId = intval($_GET['cat_id']);
            $res = $this->productModel->getProductsByCategory($catId);
            if ($res) {
                while ($p = $res->fetch_assoc()) {
                    if (!empty($p['Sale_id'])) {
                        $productsArr[] = $p;
                    }
                }
            }

        // ✅ Không có sub_id hoặc cat_id → lấy tất cả sản phẩm có Sale_id
        } else {
            $sql = "SELECT * FROM product WHERE Sale_id IS NOT NULL";
            $res = $this->db->select($sql);
            if ($res) {
                while ($p = $res->fetch_assoc()) {
                    $productsArr[] = $p;
                }
            }
        }

        // ✅ Gắn tên giảm giá cho mỗi sản phẩm (nếu thiếu)
        foreach ($productsArr as &$p) {
            if (!isset($p['sale_name'])) {
                $saleId = intval($p['Sale_id']);
                if ($saleId > 0) {
                    $saleNameResult = $this->db->select("SELECT name FROM sale WHERE id = $saleId LIMIT 1");
                    if ($saleNameResult && $row = $saleNameResult->fetch_assoc()) {
                        $p['sale_name'] = $row['name'];
                    } else {
                        $p['sale_name'] = null;
                    }
                } else {
                    $p['sale_name'] = null;
                }
            }
        }
        unset($p);

        // ✅ Xử lý sắp xếp theo giá sau giảm
        $sort = $_GET['sort'] ?? '';
        if (!empty($sort) && !empty($productsArr)) {
            foreach ($productsArr as &$p) {
                $percent = 0;
                if (!empty($p['sale_name']) && preg_match('/(\d+)%/', $p['sale_name'], $matches)) {
                    $percent = intval($matches[1]);
                }
                $p['final_price'] = $p['price'] * (1 - $percent / 100);
            }
            unset($p);

            switch ($sort) {
                case 'price_asc':
                    usort($productsArr, fn($a, $b) => $a['final_price'] <=> $b['final_price']);
                    break;
                case 'price_desc':
                    usort($productsArr, fn($a, $b) => $b['final_price'] <=> $a['final_price']);
                    break;
                case 'latest':
                    usort($productsArr, fn($a, $b) => strtotime($b['createAt']) <=> strtotime($a['createAt']));
                    break;
            }
        }
           $limit = 32;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total = count($productsArr);
        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $productsArr = array_slice($productsArr, $offset, $limit);



        include __DIR__ . '/../view/Sale.php';
    }
}