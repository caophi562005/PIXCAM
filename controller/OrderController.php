<?php
require_once __DIR__ . '/../models/OrderModel.php';

class OrderController {

    /**
     * Hiển thị lịch sử đơn hàng của người dùng
     */
    public function history() {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }

        // Lấy thông tin người dùng từ session
        $accountId = $_SESSION['user_id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;  // Giới hạn số lượng đơn hàng mỗi trang
        $offset = ($page - 1) * $limit;

        // Khởi tạo OrderModel để lấy dữ liệu lịch sử đơn hàng
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrderHistory($accountId, $page, $limit); // Lấy đơn hàng của người dùng
        $totalOrders = $orderModel->getTotalOrders($accountId);
        $totalPages = ceil($totalOrders / $limit); // Tính tổng số trang

        // Truyền dữ liệu vào view
        include __DIR__ . '/../view/orderHistory.php';
    }

    /**
     * Hiển thị chi tiết đơn hàng
     */
    public function detail() {
        // Kiểm tra nếu người dùng đã đăng nhập và có ID đơn hàng
        if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }

        $accountId = $_SESSION['user_id'];
        $orderId = $_GET['id'];

        // Khởi tạo OrderModel và lấy chi tiết đơn hàng
        $orderModel = new OrderModel();
        $orderDetails = $orderModel->getOrderDetails($accountId, $orderId);

        // Truyền dữ liệu vào view
        include __DIR__ . '/../view/orderDetail.php';
    }
}
?>
