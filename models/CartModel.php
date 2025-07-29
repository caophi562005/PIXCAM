<?php
require_once 'lib/Database.php';
class CartModel {
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    //Thêm hoặc cập nhật item vào giỏ hàng
    public function addItem($accountId, $productId, $qty, $price, $productName, $color, $size, $imgURL, $total) {
        $sql = "SELECT id, quantity FROM Cart 
                WHERE account_id=? AND product_id=? AND product_name=? AND color=? AND size=?";
        $res = $this->db->prepareSelect($sql, [
            $accountId, $productId, $productName, $color, $size
        ], "iisss");

        if ($res && $res->num_rows) {
            $row = $res->fetch_assoc();
            $newQty   = $row['quantity'] + $qty;
            $newTotal = $price * $newQty;
            $this->db->prepareUpdate(
                "UPDATE Cart SET quantity=?, total=? WHERE id=?",
                [ $newQty, $newTotal, $row['id'] ],
                "idi"
            );
        } else {
            $this->db->prepareInsert(
                "INSERT INTO Cart
                 (product_id, account_id, quantity, price, imgURL,
                  product_name, color, size, total)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [ $productId, $accountId, $qty, $price, $imgURL,
                  $productName, $color, $size, $total ],
                "iiidssssd"
            );
        }
    }

    //Lấy danh sách items trong giỏ của account
    public function getCartItems($accountId) {
        $sql = "SELECT * FROM Cart WHERE account_id = ? ORDER BY id DESC";
        $res = $this->db->prepareSelect($sql, [$accountId], "i");
        $items = [];
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $items[] = $r;
            }
        }
        return $items;
    }

    //Thay đổi số lượng (delta: +1 hoặc -1), nhưng luôn giữ minimum = 1
    public function changeQuantity($cartId, $delta) {
        $sql = "SELECT quantity, price FROM Cart WHERE id = ?";
        $res = $this->db->prepareSelect($sql, [$cartId], "i");
        if ($res && $res->num_rows) { 
            $row = $res->fetch_assoc();
            // Tính số lượng mới, tối thiểu (max) là 1
            $newQty   = max(1, $row['quantity'] + $delta);
            $newTotal = $row['price'] * $newQty;
            $this->db->prepareUpdate(
                "UPDATE Cart SET quantity = ?, total = ? WHERE id = ?",
                [$newQty, $newTotal, $cartId],
                "idi"
            );
        }
    }
   
    //Xóa một mục khỏi giỏ hàng
    public function deleteItem($cartId) {
        $this->db->prepareUpdate(
            "DELETE FROM Cart WHERE id = ?",
            [ $cartId ],
            "i"
        );
    }
    
    //Gán coupon cho toàn bộ giỏ của user
    public function applyCouponToCart(int $accountId, int $couponId): void {
    $sql = "UPDATE Cart
            SET coupon_id = ?
            WHERE account_id = ?";
    $this->db->prepareUpdate($sql, [$couponId, $accountId], "ii");
    }
    
    //Gỡ coupon (đặt lại NULL) cho toàn bộ giỏ của user
    public function removeCouponFromCart(int $accountId): void {
    $sql = "UPDATE Cart
            SET coupon_id = NULL
            WHERE account_id = ?";
    $this->db->prepareUpdate($sql, [$accountId], "i");
    }
}