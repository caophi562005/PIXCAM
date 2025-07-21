<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/PayModel.php';
require_once __DIR__ . '/../models/CouponAdminModel.php';
require_once __DIR__ . '/../models/AccountModel.php';

class PayController
{
    //Hiển thị trang thanh toán
    public function index()
    {
        // Nếu chưa đăng nhập, chuyển về login
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        $accountId = $_SESSION['user_id'];
        $account   = (new AccountModel())->getById($accountId);
        $couponM = new CouponAdminModel();
        $couponM->purgeExpired();
        if (!empty($_SESSION['applied_coupon'])) {
            $ap    = $_SESSION['applied_coupon'];
            $valid = array_column($couponM->getValid(), 'id');
            if (!in_array($ap['id'], $valid, true)) {
                unset($_SESSION['applied_coupon']);
            }
        }
        $items      = (new CartModel())->getCartItems($accountId);
        $subtotal   = array_sum(array_column($items, 'total'));
        $coupon     = $_SESSION['applied_coupon'] ?? null;
        $discount   = ($coupon && $subtotal >= $coupon['min_order'])
                    ? round($subtotal * $coupon['percent'] / 100)
                    : 0;
        $finalTotal = $subtotal - $discount;
        include __DIR__ . '/../view/Pay.php';
    }

    //Xử lý xác nhận đơn hàng
    public function confirm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }
        // Lấy thông tin người dùng và thông tin form từ POST
        $accountId     = $_SESSION['user_id'];
        $name          = trim($_POST['name']);
        $phone         = trim($_POST['phone']);
        $email         = trim($_POST['email']);
        $address       = trim($_POST['address']);
        $note          = trim($_POST['note'] ?? '');
        $paymentMethod = $_POST['paymentMethod'];
        // Kiểm tra và loại coupon hết hạn
        $couponM = new CouponAdminModel();
        $couponM->purgeExpired();
        if (!empty($_SESSION['applied_coupon'])) {
            $ap    = $_SESSION['applied_coupon'];
            $valid = array_column($couponM->getValid(), 'id');
            if (!in_array($ap['id'], $valid, true)) {
                unset($_SESSION['applied_coupon']);
            }
        }
        // Lấy giỏ hàng, tính subtotal, discount và finalTotal
        $items      = (new CartModel())->getCartItems($accountId);
        $subtotal   = array_sum(array_column($items, 'total'));
        $coupon     = $_SESSION['applied_coupon'] ?? null;
        $discount   = ($coupon && $subtotal >= $coupon['min_order'])
                    ? round($subtotal * $coupon['percent'] / 100)
                    : 0;
        $finalTotal = $subtotal - $discount;
        // Nếu phương thức thanh toán là chuyển khoản
        if ($paymentMethod === 'Chuyển khoản') {
            // Tạo orderCode duy nhất và lưu tạm dữ liệu vào session
            $orderCode = uniqid('DH');
            $_SESSION['pending_payment'] = [
                'orderCode'  => $orderCode,
                'name'       => $name,
                'phone'      => $phone,
                'email'      => $email,
                'address'    => $address,
                'note'       => $note,
                'items'      => $items,
                'subtotal'   => $subtotal,
                'discount'   => $discount,
                'finalTotal' => $finalTotal,
            ];
            header("Location: index.php?controller=pay&action=transfer");
            exit;
        }
        // Nếu thanh Tiền mặt, lưu đơn ngay    
        $this->saveOrder(
            $accountId,
            $name,
            $address,
            $phone,
            $email,
            $paymentMethod,
            $items,
            $subtotal,
            $coupon['id'] ?? null,
            $discount,
            $finalTotal,
            $note
        );
        header("Location: index.php?controller=pay&action=orderSuccess");
        exit;
    }

    //Trang hướng dẫn chuyển khoản
    public function transfer()
    {
        if (empty($_SESSION['pending_payment']) || !isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }
        $data      = $_SESSION['pending_payment'];
        $orderCode = $data['orderCode'];
        extract($data); 
        include __DIR__ . '/../view/Transfer.php';
    }

    //Xác nhận đã chuyển khoản 
    public function transferConfirm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['pending_payment'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid']);
            exit;
        }
        session_write_close(); 
        $data      = $_SESSION['pending_payment'];
        $accountId = $_SESSION['user_id'];
        $coupon    = $_SESSION['applied_coupon'] ?? null;
        // Lưu đơn sau khi chuyển khoản
        $this->saveOrder(
            $accountId,
            $data['name'],
            $data['address'],
            $data['phone'],
            $data['email'],
            'Chuyển khoản',
            $data['items'],
            $data['subtotal'],
            $coupon['id'] ?? null,
            $data['discount'],
            $data['finalTotal'],
            $data['note']
        );
        unset($_SESSION['pending_payment']);
        echo json_encode([
            'success'  => true,
            'redirect' => 'index.php?controller=pay&action=orderSuccess'
        ]);
        exit;
    }

    //Hủy đơn chuyển khoản
    public function cancelTransfer()
    {
        unset($_SESSION['pending_payment']);
        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    //Lưu đơn hàng và chi tiết sản phẩm
  private function saveOrder(
    $accountId,
    $name,
    $address,
    $phone,
    $email,
    $paymentMethod,
    $items,
    $subtotal,
    $couponId,
    $discount,
    $finalTotal,
    $note
) {
    $payModel = new PayModel();

    // B1. Check tồn kho TẤT CẢ sản phẩm trước
    foreach ($items as $it) {
    $currentStock = $payModel->checkProductQuantity((int)$it['product_id']);

    // Nếu không tìm thấy sản phẩm hoặc không đủ hàng
    if ($currentStock === null || $it['quantity'] > $currentStock) {
        $productNameSafe = htmlspecialchars($it['product_name'], ENT_QUOTES, 'UTF-8');
        echo "<script>
            alert('Sản phẩm {$productNameSafe} chỉ còn {$currentStock} sản phẩm trong kho. Vui lòng kiểm tra lại giỏ hàng.');
            window.location.href = 'index.php?controller=cart&action=index';
        </script>";
        exit;
    }
}

    // B2. Sau khi chắc chắn đủ hàng, mới tạo order
    $pid = $payModel->createOrder(
        $accountId,
        $name,
        $address,
        $phone,
        $email,
        $paymentMethod,
        $subtotal,
        $couponId,
        $discount,
        $finalTotal,
        $note
    );

    // B3. Lưu chi tiết + trừ tồn kho
    foreach ($items as $it) {
        $payModel->addDetail(
            $pid,
            (int)$it['product_id'],
            $it['product_name'],
            (int)$it['price'],
            (int)$it['quantity'],
            (int)$it['total']
        );
        $payModel->updateProductQuantity(
            (int)$it['product_id'],
            (int)$it['quantity']
        );
    }

    unset($_SESSION['applied_coupon'], $_SESSION['pending_payment']);
    $payModel->clearCart($accountId);
}



    //Trang hiển thị thành công đơn hàng
    public function orderSuccess()
    {
        // Đảm bảo người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        $accountId   = $_SESSION['user_id'];
        $payModel    = new PayModel();
        $paymentId   = $payModel->getLastPaymentId($accountId);
        if (!$paymentId) {
            echo "Không tìm thấy đơn hàng.";
            return;
        }
        // Lấy thông tin và chi tiết đơn
        $paymentInfo  = $payModel->getPaymentInfo($paymentId);
        $orderDetails = $payModel->getOrderDetails($paymentId);
        include __DIR__ . '/../view/OrderSuccess.php';
    }
}