<?php
require_once __DIR__ . '/../lib/Database.php';

class OrderModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy lịch sử đơn hàng của người dùng, nhóm theo payment_id
     */
    public function getOrderHistory(int $accountId, int $page = 1, int $limit = 10): array {
        if ($page < 1 || $limit < 1) {
            return [];
        }

        $offset = ($page - 1) * $limit;
        $sql = "
            SELECT p.id AS payment_id,
                   p.createAt,
                   p.total,
                   p.finalTotal,
                   p.paymentMethod
            FROM payment p
            WHERE p.account_id = ?
            ORDER BY p.createAt DESC
            LIMIT ? OFFSET ?
        ";

        $result = $this->db->prepareSelect($sql, [$accountId, $limit, $offset], "iii");
        if ($result === false) {
            return [];
        }

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            // gắn thêm mảng products cho mỗi đơn
            $row['products'] = $this->getProductsForOrder($row['payment_id']);
            $orders[] = $row;
        }

        return $orders;
    }
    public function getLastPaymentId(int $accountId, int $productId): ?int {
    $sql = "
      SELECT p.payment_id
      FROM (
        SELECT payment_id
        FROM pay
        WHERE product_id=? 
      ) AS p
      JOIN payment pm ON pm.id = p.payment_id
      WHERE pm.account_id=?
      ORDER BY pm.createAt DESC
      LIMIT 1
    ";
    $res = $this->db->prepareSelect($sql, [$productId, $accountId], "ii");
    if ($res && $r = $res->fetch_assoc()) {
        return (int)$r['payment_id'];
    }
    return null;
}

    /**
     * Lấy sản phẩm cho một đơn hàng cụ thể
     */
    private function getProductsForOrder(int $paymentId): array {
        $sql = "
            SELECT pay.product_id,
                   pay.productName,
                   pay.quantity,
                   pay.price,
                   pay.total AS productTotal
            FROM pay
            WHERE pay.payment_id = ?
        ";

        $result = $this->db->prepareSelect($sql, [$paymentId], "i");
        if ($result === false) {
            return [];
        }

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }

    /**
     * Lấy tổng số đơn hàng của người dùng
     */
    public function getTotalOrders(int $accountId): int {
        $sql = "
            SELECT COUNT(*) AS total
            FROM payment p
            WHERE p.account_id = ?
        ";

        $result = $this->db->prepareSelect($sql, [$accountId], "i");
        if ($result === false) {
            return 0;
        }

        $row = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Lấy chi tiết một đơn hàng (thông tin payment + mảng products)
     * Trả về null nếu đơn không tồn tại hoặc không thuộc về user
     */
    public function getOrderDetails(int $accountId, int $paymentId): ?array {
        // 1. Lấy thông tin chính của đơn từ table payment
        $sql = "
            SELECT id           AS payment_id,
                   createAt,
                   total,
                   discount_amount,
                   finalTotal,
                   paymentMethod,
                   name,
                   address,
                   phone,
                   email,
                   note
            FROM payment
            WHERE id = ?
              AND account_id = ?
        ";
        $result = $this->db->prepareSelect($sql, [$paymentId, $accountId], "ii");
        if ($result === false) {
            return null;
        }

        $details = $result->fetch_assoc();
        if (! $details) {
            return null;
        }

        // 2. Gắn mảng sản phẩm
        $details['products'] = $this->getProductsForOrder($paymentId);

        return $details;
    }
}
