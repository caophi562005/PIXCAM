<?php
require_once __DIR__ . '/../models/ProductModel.php';

class HomeController {
    public function index() {
        if (isset($_SESSION['permission_required'])) {
            $permission_required_message = $_SESSION['permission_required'];
             // Xóa session để thông báo chỉ hiển thị 1 lần
            unset($_SESSION['permission_required']);
            echo "<script>window.onload = function() { alert('$permission_required_message'); }</script>";
        }
        $productModel = new ProductModel();
        // Lấy 12 sản phẩm mới nhất
        $products = $productModel->getNewestProducts(12);

        // Truyền mảng $products vào view
        include __DIR__ . '/../view/index.php';
    }
}