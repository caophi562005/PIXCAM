<?php
require_once 'lib/database.php';

class CouponAdminModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    //Vô hiệu hoá coupon đã hết hạn để không còn đc áp dụng
    public function purgeExpired() {
        $sql = "UPDATE Coupon
                SET is_active = 0
                WHERE expiration IS NOT NULL
                  AND expiration < NOW()";
        $this->db->prepareUpdate($sql, [], "");
    }

    //Lấy danh sách toàn bộ coupon còn hiệu lực
    public function getAll() {
        $this->purgeExpired();
        $sql = "SELECT *
                FROM Coupon
                ORDER BY created_at DESC";
        $res = $this->db->prepareSelect($sql, [], "");
        $items = [];
        while ($res && $row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    //Lấy danh sách coupon hợp lệ (đang hoạt động và chưa hết hạn)
    public function getValid() {
        $this->purgeExpired();
        $sql = "SELECT *
                FROM Coupon
                WHERE is_active = 1
                  AND expiration IS NOT NULL
                  AND expiration >= NOW()
                ORDER BY expiration ASC";
        $res = $this->db->prepareSelect($sql, [], "");
        $items = [];
        while ($res && $row = $res->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }

    //Thêm một coupon mới
    public function add($code, $percent, $minOrder = 0) {
        $sql = "INSERT INTO Coupon (code, percent, min_order) VALUES (?, ?, ?)";
        return $this->db->prepareInsert($sql, [$code, $percent, $minOrder], "sii");
    }

    //Xóa coupon theo mã
    //Trước khi xóa, gỡ tham chiếu coupon trong Payment và Cart để tránh lỗi ràng buộc
    public function deleteByCodes(array $codes) {
        if (empty($codes)) return;
        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $types = str_repeat('s', count($codes));
        //Gỡ tham chiếu trong Payment
        $sqlClearPayment = "
            UPDATE Payment p
            JOIN Coupon c ON p.coupon_id = c.id
            SET p.coupon_id = NULL
            WHERE c.code IN ($placeholders)
        ";
        $this->db->prepareUpdate($sqlClearPayment, $codes, $types);
        //Gỡ tham chiếu trong Cart
        $sqlClearCart = "
            UPDATE Cart ct
            JOIN Coupon c ON ct.coupon_id = c.id
            SET ct.coupon_id = NULL
            WHERE c.code IN ($placeholders)
        ";
        $this->db->prepareUpdate($sqlClearCart, $codes, $types);
        //Xóa coupon
        $sqlDel = "DELETE FROM Coupon WHERE code IN ($placeholders)";
        $this->db->prepareUpdate($sqlDel, $codes, $types);
    }

    // Đặt hạn sử dụng cho coupon và kích hoạt coupon
    public function scheduleByCodes(array $codes, $datetime) {
        if (empty($codes)) return;
        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $types = 's' . str_repeat('s', count($codes));
        $params = array_merge([$datetime], $codes);
        $sql = "UPDATE Coupon SET expiration = ?, is_active = 1 WHERE code IN ($placeholders)";
        $this->db->prepareUpdate($sql, $params, $types);

    }

    //Kiểm tra có tồn tại mã coupon trong database
    public function existsCode(string $code): bool {
        $sql = "SELECT COUNT(*) AS cnt FROM Coupon WHERE code = ?";
        $res = $this->db->prepareSelect($sql, [$code], "s");
        if ($res && $row = $res->fetch_assoc()) {
            return $row['cnt'] > 0;
        }
        return false;
    }
    
    //Vô hiệu hoá coupon theo mã (nếu cần)
    public function deactivate($code) {
        $sql = "UPDATE Coupon SET is_active = 0 WHERE code = ?";
        $this->db->prepareUpdate($sql, [$code], "s");
    }
}