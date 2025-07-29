
<?php
require_once __DIR__ . '/../lib/Database.php';

class FeedbackModel {
    private Database $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Kiểm tra xem user đã feedback cho product trong đơn payment chưa
     */
    public function hasFeedback(int $accountId, int $productId, int $paymentId): bool {
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM feedback
            WHERE account_id = ? AND product_id = ? AND payment_id = ?
        ";
        $res = $this->db->prepareSelect(
            $sql,
            [ $accountId, $productId, $paymentId ],
            "iii"
        );
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['cnt'] > 0;
        }
        return false;
    }

    /**
     * Thêm feedback mới
     */
    public function add(int $accountId, int $productId, int $paymentId, int $rating, string $comment): bool {
        $sql = "
            INSERT INTO feedback
              (account_id, product_id, payment_id, rating, comment)
            VALUES (?, ?, ?, ?, ?)
        ";
        return $this->db->prepareInsert(
            $sql,
            [ $accountId, $productId, $paymentId, $rating, $comment ],
            "iiiis"
        );
    }

    /**
     * Cập nhật feedback đã tồn tại
     */
    public function update(int $accountId, int $productId, int $paymentId, int $rating, string $comment): bool {
    $sql = "
        UPDATE feedback
        SET rating     = ?,
            comment    = ?,
            is_edited  = 1,
            updated_at = NOW()
        WHERE account_id = ?
          AND product_id = ?
          AND payment_id = ?
    ";
    return $this->db->prepareUpdate(
        $sql,
        [ $rating, $comment, $accountId, $productId, $paymentId ],
        "isiii"
    );
}
    /**
     * Lấy danh sách feedback theo product
     */
    public function listByProduct(int $productId): array {
        $sql = "
            SELECT f.*, a.username
            FROM feedback f
            JOIN account a ON f.account_id = a.id
            WHERE f.product_id = ?
            ORDER BY f.created_at DESC
        ";
        $res = $this->db->prepareSelect(
            $sql,
            [ $productId ],
            "i"
        );
        if (!$res) {
            error_log("FeedbackModel::listByProduct SQL error.");
            return [];
        }
        $out = [];
        while ($row = $res->fetch_assoc()) {
            $out[] = $row;
        }
        return $out;
    }

    public function hasEdited(int $accountId, int $productId, int $paymentId): bool {
    $sql = "
      SELECT is_edited
      FROM feedback
      WHERE account_id = ? AND product_id = ? AND payment_id = ?
    ";
    $res = $this->db->prepareSelect($sql, [$accountId, $productId, $paymentId], "iii");
    if ($res && $row = $res->fetch_assoc()) {
        return (int)$row['is_edited'] === 1;
    }
    return false;
}
/**
 * Lấy 1 feedback theo user – product – payment
 */
public function getFeedback(int $accountId, int $productId, int $paymentId): ?array {
    $sql = "
        SELECT rating, comment, is_edited, created_at, updated_at
        FROM feedback
        WHERE account_id = ? AND product_id = ? AND payment_id = ?
        LIMIT 1
    ";
    $res = $this->db->prepareSelect($sql, [$accountId, $productId, $paymentId], 'iii');
    if ($res && $row = $res->fetch_assoc()) {
        return $row;
    }
    return null;
}

    /**
     * Thống kê feedback theo product
     */
    public function getStatsByProduct(int $productId): array {
        $sql = "
            SELECT
              COUNT(*) AS total,
              ROUND(AVG(rating),1) AS avg_rating,
              SUM(CASE WHEN comment <> '' THEN 1 ELSE 0 END) AS with_comment
            FROM feedback
            WHERE product_id = ?
        ";
        $res = $this->db->prepareSelect(
            $sql,
            [ $productId ],
            "i"
        );
        $r = $res ? $res->fetch_assoc() : ['total'=>0,'avg_rating'=>0,'with_comment'=>0];

        $stars = [];
        for ($i = 1; $i <= 5; $i++) {
            $r2 = $this->db->prepareSelect(
                "SELECT COUNT(*) AS cnt FROM feedback WHERE product_id = ? AND rating = ?",
                [ $productId, $i ],
                "ii"
            );
            $stars[$i] = $r2 ? (int)$r2->fetch_assoc()['cnt'] : 0;
        }

        return [
            'total'        => (int)$r['total'],  
            'avg'          => (float)$r['avg_rating'],  
            'stars'        => $stars,
            'with_comment' => (int)$r['with_comment'],
        ];
    }

    /**
     * Lấy media kèm feedback
     */
    public function getMedia(int $feedbackId): array {
        $sql = "SELECT url, type FROM feedback_media WHERE feedback_id = ?";
        $res = $this->db->prepareSelect(
            $sql,
            [ $feedbackId ],
            "i"
        );
        $out = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
        }
        return $out;
    }
}

