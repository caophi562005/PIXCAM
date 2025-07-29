<?php
// controllers/RevenueController.php

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../models/RevenueModel.php';

class RevenueController {
    protected $model;

    public function __construct() {
        $db = new Database();
        $this->model = new RevenueModel($db);
    }

    public function index() {
        if ($_SESSION['role'] != 'admin') {
            // Chuyển hướng đến trang đăng nhập và thông báo
            $_SESSION['permission_required'] = "Bạn không có quyền truy cập trang này!";
            header("Location: index.php?controller=home&action=index");
            exit;
        }
        // 1) Lấy kết quả
        $dailyRes   = $this->model->getDaily();
        $monthlyRes = $this->model->getMonthly();
        $yearlyRes  = $this->model->getYearly();

        // 2) Đọc vào arrays (kiểm tra false trước khi dùng)
        $dailyRows = [];
        $labelsD = []; $dataD = [];
        if ($dailyRes !== false) {
            while ($r = $dailyRes->fetch_assoc()) {
                $dailyRows[] = $r;
                $labelsD[] = $r['day'];
                $dataD[]   = (int)$r['total'];
            }
        }

        $monthlyRows = [];
        $labelsM = []; $dataM = [];
        if ($monthlyRes !== false) {
            while ($r = $monthlyRes->fetch_assoc()) {
                $monthlyRows[] = $r;
                $labelsM[] = sprintf('%04d/%02d', $r['year'], $r['month']);
                $dataM[]   = (int)$r['total'];
            }
        }

        $yearlyRows = [];
        $labelsY = []; $dataY = [];
        if ($yearlyRes !== false) {
            while ($r = $yearlyRes->fetch_assoc()) {
                $yearlyRows[] = $r;
                $labelsY[] = (string)$r['year'];
                $dataY[]   = (int)$r['total'];
            }
        }

        // 3) Gọi view
        require_once __DIR__ . '/../view/revenue.php';
    }
}