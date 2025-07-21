<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../lib/Database.php';

class FindProductController {
    private $productModel;
    private $db; // Thêm thuộc tính $db để dùng chung

    public function __construct() {
        $this->productModel = new ProductModel();
        $this->db = new Database(); // Khởi tạo Database tại controller
    }

    public function index() {
        // 1. Lấy từ khóa và sort từ $_GET
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $sort    = isset($_GET['sort']) ? trim($_GET['sort']) : '';

        // 2. Dò tìm sản phẩm
        $productsArr = [];
        if ($keyword !== '') {
            $productsArr = $this->productModel->findProductsByName($keyword);
        }

        // 3. Gắn sale_name nếu thiếu (dùng $this->db)
        foreach ($productsArr as &$p) {
            if (!isset($p['sale_name'])) {
                $saleId = intval($p['Sale_id']);
                if ($saleId > 0) {
                    // Chuyển thành $this->db->select(...)
                    $saleNameResult = $this->db->select(
                        "SELECT name FROM sale WHERE id = $saleId LIMIT 1"
                    );
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

        // 4. Tính final_price cho từng sản phẩm
      foreach ($productsArr as &$p) {
    // Nếu có price_sale do Model đã tính, dùng nó
    if (isset($p['price_sale'])) {
        $p['final_price'] = floatval($p['price_sale']);
    } else {
        // Nếu không có, tính giảm giá từ sale_name
        $percent = 0;
        if (!empty($p['sale_name']) && preg_match('/(\d+)%/', $p['sale_name'], $matches)) {
            $percent = intval($matches[1]);
        }
        $p['final_price'] = floatval($p['price']) * (1 - $percent / 100);
    }
}
unset($p);


        // 5. Sắp xếp theo sort nếu có
        if (!empty($sort) && !empty($productsArr)) {
            switch ($sort) {
                case 'price_asc':
                    usort($productsArr, fn($a, $b) => $a['final_price'] <=> $b['final_price']);
                    break;
                case 'price_desc':
                    usort($productsArr, fn($a, $b) => $b['final_price'] <=> $a['final_price']);
                    break;
                case 'latest':
                    usort(
                        $productsArr,
                        fn($a, $b) => strtotime($b['createAt']) <=> strtotime($a['createAt'])
                    );
                    break;
            }
        }
           $limit = 32;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $total = count($productsArr);
        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;

        $productsArr = array_slice($productsArr, $offset, $limit);


        // 6. Load view
        include __DIR__ . '/../view/Findproduct.php';
    }
}