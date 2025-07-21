<?php
require_once __DIR__ . '/../models/CouponAdminModel.php';

class CouponAdminController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CouponAdminModel();
    }

    //Hiển thị trang quản lý coupon cho Admin
    public function index()
    {
        if ($_SESSION['role'] !== 'admin') {
            $_SESSION['permission_required'] = "Bạn không có quyền truy cập trang này!";
            header("Location: index.php?controller=home&action=index");
            exit;
        }
        $this->model->purgeExpired();
        $coupons = $this->model->getAll();
        include __DIR__ . '/../view/CouponAdmin.php';
    }

    //Xử lý các hành động POST từ form quản lý coupon
    public function handle()
    {
        if (isset($_POST['_action']) && $_POST['_action'] === 'add') {
            $code      = trim($_POST['code']);
            $percent   = intval($_POST['percent']);
            $min_order = intval($_POST['min_order'] ?? 0);
            if ($this->model->existsCode($code)) {
                $_SESSION['coupon_error'] = "Mã <strong>$code</strong> đã tồn tại!";
            } else {
                $this->model->add($code, $percent, $min_order);
                $_SESSION['coupon_success'] = "Thêm mã <strong>$code</strong> thành công!";
            }
        }
        if (isset($_POST['_action']) && $_POST['_action'] === 'delete' && !empty($_POST['selectedCodes'])) {
            $this->model->deleteByCodes($_POST['selectedCodes']);
            $_SESSION['coupon_success'] = "Xóa thành công " . count($_POST['selectedCodes']) . " mã.";
        }
        if (
            isset($_POST['_action']) && $_POST['_action'] === 'schedule'
            && !empty($_POST['selectedCodes'])
            && !empty($_POST['scheduledTime'])
        ) {
            $this->model->scheduleByCodes($_POST['selectedCodes'], $_POST['scheduledTime']);
            $_SESSION['coupon_success'] = "Cập nhật hạn sử dụng thành công!";
        }
        header("Location: index.php?controller=CouponAdmin&action=index");
        exit;
    }
    
    //Xử lý yêu cầu AJAX trả về danh sách coupon còn hiệu lực dưới dạng JSON
    public function getValidAjax()
    {
        $this->model->purgeExpired();
        $list = $this->model->getValid();
        header('Content-Type: application/json');
        echo json_encode($list);
        exit;
    }
}