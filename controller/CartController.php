<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CouponAdminModel.php';
require_once __DIR__ . '/../models/PayModel.php';

class CartController
{
    //Hiển thị trang giỏ hàng
    public function index()
    {
        //Kiểm tra đã đăng nhập chưa
        if (!isset($_SESSION['username'])) {
            $_SESSION['login_required'] = "Bạn cần đăng nhập để xem giỏ hàng!";
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        //Kiểm tra và loại bỏ coupon hết hạn dựa vào expiration so với thời gian hiện tại
        if (!empty($_SESSION['applied_coupon'])) {
            $c   = $_SESSION['applied_coupon'];
            $exp = !empty($c['expiration'])
                ? new DateTime($c['expiration'], new DateTimeZone('Asia/Ho_Chi_Minh'))
                : null;
            $now = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
            if ($exp !== null && $exp < $now) {
                unset($_SESSION['applied_coupon']);
            }
        }
        //Lấy coupon đang áp dụng (nếu có)
        $coupon = $_SESSION['applied_coupon'] ?? null;
        //Kiểm tra coupon với Database đảm bảo admin chưa hủy hoặc xóa coupon đó
        if ($coupon) {
            $couponM  = new CouponAdminModel();
            $validIds = array_column($couponM->getValid(), 'id');
            if (!in_array((int)$coupon['id'], $validIds, true)) {
                unset($_SESSION['applied_coupon']);
                $coupon = null;
                $accountId = $_SESSION['user_id'];
                $cartModel = new CartModel();
                $cartModel->removeCouponFromCart($accountId);
            }
        }
        //Lấy dữ liệu các mục trong giỏ hàng từ model
        $accountId = $_SESSION['user_id'];
        $cartModel = new CartModel();
        $cartItems = $cartModel->getCartItems($accountId);
        $subTotal = array_sum(array_column($cartItems, 'total'));
        $discount = 0;
        $grand    = $subTotal;
        //Nếu có coupon và subtotal đạt điều kiện min_order thì tính discount
        if ($coupon && $subTotal >= $coupon['min_order']) {
            $discount = round($subTotal * $coupon['percent'] / 100);
            $grand    = max(0, $subTotal - $discount);
        }
        include __DIR__ . '/../view/Cart.php';
    }

    //Thêm sản phẩm vào giỏ hàng
    public function add()
    {
        //Kiểm tra đăng nhập
        if (!isset($_SESSION['username'])) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập!']);
                exit;
            }
            $_SESSION['login_required'] = "Bạn cần đăng nhập để thêm sản phẩm!";
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        // Lấy dữ liệu từ POST
        $productId = intval($_POST['product_id']);
        $color     = htmlspecialchars(trim($_POST['color']));
        $qty       = max(1, intval($_POST['qty']));
        $size      = !empty($_POST['size']) ? htmlspecialchars(trim($_POST['size'])) : '';
        $accountId = $_SESSION['user_id'];
        //Kiểm tra tồn tại sản phẩm
        $prodModel = new ProductModel();
        $p         = $prodModel->getProductById($productId);
        if (!$p) {
            if ($this->isAjax()) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
                exit;
            }
            $_SESSION['cart_error'] = "Sản phẩm không tồn tại!";
            header("Location: index.php");
            exit;
        }
        $price  = $p['price_sale'] ?? $p['price']; //giá giảm(nếu có), ngược lại giá gốc
        $name   = $p['name'] . ($size ? " - $size" : "") . " ($color)";
        $total  = $price * $qty;
        $imgURL = $p['imgURL_1'];
        //Thêm item vào giỏ qua CartModel
        $cartModel = new CartModel();
        $cartModel->addItem($accountId, $productId, $qty, $price, $name, $color, $size, $imgURL, $total);
        if ($this->isAjax()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Sản phẩm đã được thêm vào giỏ hàng']);
            exit;
        }
        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    //Cập nhật số lượng 
    public function update()
    {
        //Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        //Xử lý yêu cầu thay đổi số lượng
        if (isset($_POST['change_qty'])) {
            list($action, $id) = explode('-', $_POST['change_qty']);
            $id    = intval($id);
            $delta = ($action === 'plus') ? 1 : -1;
            //Cập nhật Database thông qua Model
            $cartModel = new CartModel();
            $cartModel->changeQuantity($id, $delta);
            //Nếu là AJAX, trả về JSON với dữ liệu mới
            if ($this->isAjax()) {
                $items    = $cartModel->getCartItems($_SESSION['user_id']);
                $subTotal = array_sum(array_column($items, 'total'));
                $grand    = $subTotal;

                //Tìm lại số lượng và tổng tiền của item vừa thay đổi
                $newQty    = 0;
                $itemTotal = 0;
                foreach ($items as $it) {
                    if ($it['id'] === $id) {
                        $newQty    = $it['quantity'];
                        $itemTotal = $it['total'];
                        break;
                    }
                }
                header('Content-Type: application/json');
                echo json_encode([
                    'success'    => true,
                    'itemId'     => $id,
                    'newQty'     => $newQty,
                    'itemTotal'  => $itemTotal,
                    'subTotal'   => $subTotal,
                    'grandTotal' => $grand
                ]);
                exit;
            }
        }
        header("Location: index.php?controller=cart&action=index");
        exit;
    }
    
    //Xóa một sản phẩm
    public function delete()
    {
        //Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?controller=login&action=index");
            exit;
        }
        //Lấy id item cần
        $id = intval($_GET['id'] ?? 0);
        //Nếu id hợp lệ, gọi model để xóa
        if ($id > 0) {
            $cartModel = new CartModel();
            $cartModel->deleteItem($id);
        }
        if ($this->isAjax()) {
            //Lấy lại danh sách sản phẩm trong giỏ hàng sau khi xóa
            $items    = $cartModel->getCartItems($_SESSION['user_id']);
            $subTotal = array_sum(array_column($items, 'total'));
            $grand    = $subTotal;
            header('Content-Type: application/json');
            echo json_encode([ 
                'success'    => true,
                'deletedId'  => $id,
                'subTotal'   => $subTotal,
                'grandTotal' => $grand
            ]);
            exit;
        }
        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    //Áp dụng coupon
    public function applyCoupon()
    {
        if (!$this->isAjax()) {
            header('HTTP/1.1 400 Bad Request');
            exit;
        }
        $code      = trim($_POST['code'] ?? '');
        $couponM   = new CouponAdminModel();
        $payModel  = new PayModel();
        $accountId = $_SESSION['user_id'] ?? null;

        // Kiểm tra đăng nhập
        if (!$accountId) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để áp dụng coupon']);
            exit;
        }
        // Lấy danh sách coupon còn hiệu lực
        $list     = $couponM->getValid();
        $cartM    = new CartModel();
        $items    = $cartM->getCartItems($accountId);
        $subTotal = array_sum(array_column($items, 'total'));
        foreach ($list as $c) {
            if ($c['code'] === $code) {
                if ($payModel->hasUsedCoupon($accountId, intval($c['id']))) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Bạn đã sử dụng mã này rồi, không thể áp dụng lại.'
                    ]);
                    exit;
                }
                // Kiểm tra đơn tối thiểu
                if ($subTotal < $c['min_order']) {
                    echo json_encode(['success' => false, 'message' => 'Bạn chưa đủ điều kiện để áp dụng mã']);
                    exit;
                }
                // Áp mã thành công
                $_SESSION['applied_coupon'] = $c;
                $cartM->applyCouponToCart($accountId, intval($c['id']));
                echo json_encode(['success' => true, 'percent' => intval($c['percent'])]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Coupon không tồn tại hoặc đã hết hạn']);
        exit;
    }

    //Hủy coupon đã áp dụng
    public function removeCoupon()
    {
        $cartModel = new CartModel();
        $cartModel->removeCouponFromCart($_SESSION['user_id']);
        unset($_SESSION['applied_coupon']);

        if ($this->isAjax()) {
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    //Kiểm tra request AJAX
    private function isAjax()
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        );
    }
}