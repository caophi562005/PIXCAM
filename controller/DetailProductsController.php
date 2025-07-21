<?php
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/FeedbackModel.php';
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../lib/Database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class DetailProductsController {
    private $productModel;
    private $feedbackModel;
    private $orderModel;

    public function __construct() {
        $this->productModel  = new ProductModel();
        $this->feedbackModel = new FeedbackModel();
        $this->orderModel    = new OrderModel();
    }

   public function index() {
    // 1. Lấy product.id từ URL
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        header("Location: index.php");
        exit;
    }

    // 2. Lấy chi tiết sản phẩm
    $product = $this->productModel->getProductById($id);
    if (!$product) {
        header("Location: index.php");
        exit;
    }

    // 3. Lấy sản phẩm liên quan
    $subCatId = intval($product['subCategory_id']);
    $relatedProducts = $this->productModel->getRelatedProducts($subCatId, $id, 4);

    // 4. Lấy toàn bộ feedback và thống kê
    $allFeedbacks    = $this->feedbackModel->listByProduct($id);
    $feedbackStats   = $this->feedbackModel->getStatsByProduct($id);
    $stats           = $feedbackStats; // Gửi sang view để dùng

    // PHÂN TRANG CHO FEEDBACK: mỗi page 6 items
    $perPage     = 6;
    $totalFb     = count($allFeedbacks);
    $totalPages  = $totalFb ? (int)ceil($totalFb / $perPage) : 1;
    $currentPage = isset($_GET['fb_page']) ? max(1, (int)$_GET['fb_page']) : 1;
    if ($currentPage > $totalPages) $currentPage = $totalPages;
    $startIndex  = ($currentPage - 1) * $perPage;
    $feedbacks   = array_slice($allFeedbacks, $startIndex, $perPage);

    // 5. Xác định xem user có thể review không
    $canReview     = false;
    $lastPaymentId = null;
    if (isset($_SESSION['user_id'])) {
        $userId        = (int)$_SESSION['user_id'];
        $lastPaymentId = $this->orderModel->getLastPaymentId($userId, $id);
        if ($lastPaymentId
            && !$this->feedbackModel->hasFeedback($userId, $id, $lastPaymentId)
        ) {
            $canReview = true;
        }
    }

    // 6. Truyền sang view
    include __DIR__ . '/../view/DetailProducts.php';
}

}