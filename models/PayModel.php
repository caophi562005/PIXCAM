<?php
require_once __DIR__ . '/../lib/database.php';

class PayModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    //Tạo một đơn hàng mới trong hệ thống và trả về payment_id vừa được tạo
    public function createOrder(
        int $accountId,
        string $name,
        string $address,
        string $phone,
        string $email,
        string $paymentMethod,
        int $subtotal,
        ?int $couponId,
        int $discountAmount,
        int $finalTotal,
        ?string $note
    ): int {
        $sql = "INSERT INTO Payment
            (account_id, name, address, phone, email, paymentMethod,
            total, coupon_id, discount_amount, finalTotal, note, createAt)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $params = [
            $accountId,
            $name,
            $address,
            $phone,
            $email,
            $paymentMethod,
            $subtotal,
            $couponId,
            $discountAmount,
            $finalTotal,
            $note
        ];
        $types = "isssssiiiis";
        $this->db->prepareInsert($sql, $params, $types);
        $res = $this->db->prepareSelect(
            "SELECT LAST_INSERT_ID() AS id",
            [],
            ""
        );
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['id'];
        }
        throw new Exception("Không lấy được payment_id sau INSERT");
    }

    //Lưu chi thông tin tiết từng sản phẩm vào bảng Pay
    public function addDetail(
            int $paymentId,
            int $productId,
            string $productName,
            int $price,
            int $quantity,
            int $total
        ): void {
            $sql = "INSERT INTO pay
                (payment_id, product_id, productName, price, quantity, total)
            VALUES (?, ?, ?, ?, ?, ?)";
            $this->db->prepareInsert($sql, [
                $paymentId,
                $productId,
                $productName,
                $price,
                $quantity,
                $total
            ], "iisiii");
    }

    //Xóa giỏ hàng sau khi đặt
    public function clearCart(int $accountId): void {
        $this->db->prepareUpdate(
            "DELETE FROM Cart WHERE account_id = ?",
            [$accountId],
            "i"
        );
    }
    
    //Kiểm tra user đã sử dụng coupon (trong các đơn trước) chưa
    public function hasUsedCoupon(int $accountId, int $couponId): bool {
        $sql = "SELECT COUNT(*) AS cnt
                FROM Payment
                WHERE account_id = ?
                  AND coupon_id  = ?";
        $res = $this->db->prepareSelect($sql, [$accountId, $couponId], "ii");
        if ($res && $row = $res->fetch_assoc()) {
            return $row['cnt'] > 0;
        }
        return false;
    }

    //Lấy thông tin chi tiết của một đơn hàng theo payment_id
    public function getPaymentInfo(int $paymentId) {
        $query = "SELECT * FROM payment WHERE id = ?";
        $params = [$paymentId];
        $types = "i"; 
        $result = $this->db->prepareSelect($query, $params, $types);
        return $result ? $result->fetch_assoc() : null;  
    }
    
    //Lấy đơn hàng mới nhất của người dùng
    public function getLastPaymentId(int $accountId) {
        $query = "SELECT id FROM payment WHERE account_id = ? ORDER BY createAt DESC LIMIT 1";
        $params = [$accountId];
        $types = "i";
        $result = $this->db->prepareSelect($query, $params, $types);
        $row = $result->fetch_assoc();
        return $row ? $row['id'] : null;
    }
    
    //Lấy chi tiết đơn hàng từ bảng pay theo payment_id
    public function getOrderDetails(int $paymentId) {
        $query = "SELECT * FROM pay WHERE payment_id = ?";
        $params = [$paymentId];
        $types = "i"; 
        return $this->db->prepareSelect($query, $params, $types); 
    }
    
    //Cập nhật số lượng sản phẩm sau khi có đơn hàng
   public function updateProductQuantity(int $productId, int $orderedQuantity): bool {
    $sql = "SELECT quantity FROM product WHERE id = ?";
    $res = $this->db->prepareSelect($sql, [$productId], "i");
    if ($res && $row = $res->fetch_assoc()) {
        $currentQuantity = (int)$row['quantity'];

        // ✅ Nếu không đủ hàng, return false
        if ($orderedQuantity > $currentQuantity) {
            return false;
        }

        // ✅ Nếu đủ, tính số lượng mới
        $newQuantity = $currentQuantity - $orderedQuantity;

        // Update lại
        $updateSql = "UPDATE product SET quantity = ? WHERE id = ?";
        $this->db->prepareUpdate($updateSql, [$newQuantity, $productId], "ii");

        return true;
    }

    // Không tìm thấy sản phẩm, coi như thất bại
    return false;
}
public function checkProductQuantity(int $productId): ?int {
    $sql = "SELECT quantity FROM product WHERE id = ?";
    $res = $this->db->prepareSelect($sql, [$productId], "i");
    if ($res && $row = $res->fetch_assoc()) {
        return (int)$row['quantity'];
    }
    return null; // không tìm thấy
}


}