<?php

require_once __DIR__ . '/../lib/database.php';

class AccountModel {
    protected $db;
    public function __construct() {
        $this->db = new Database();
    }

    /** Lấy 1 tài khoản theo id */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM `account` WHERE id = ?";
        return $this->db->fetchOne($sql, [$id], "i");
    }

    /** Cập nhật thông tin cơ bản */
    public function updateInfo(int $id, string $username, string $email, string $phone): bool {
        $sql = "UPDATE `account`
                SET username = ?, email = ?, phone = ?
                WHERE id = ?";
        return $this->db->prepareUpdate($sql, [$username,$email,$phone,$id], "sssi");
    }

    /** Cập nhật mật khẩu (đã hash) */
    public function updatePassword(int $id, string $passwordHash): bool {
        $sql = "UPDATE `account` SET passwordHash = ? WHERE id = ?";
        return $this->db->prepareUpdate($sql, [$passwordHash,$id], "si");
    }


    /** Lấy role của một account */
    public function getRolesByAccount(int $id): array {
        $sql = "SELECT r.name
                FROM role r
                JOIN accountrole ar ON ar.role_id = r.id
                WHERE ar.account_id = ?";
        $res = $this->db->prepareSelect($sql, [$id], "i");
        return $res 
            ? array_column($res->fetch_all(MYSQLI_ASSOC), 'name') 
            : [];
    }

    /** Xóa tài khoản */
   // models/AccountModel.php
public function delete(int $id): bool {
    // 0) Xóa feedback liên quan trước
    $this->db->prepareDelete(
        "DELETE FROM `feedback` WHERE account_id = ?",
        [$id],
        "i"
    );

    // ⚠️ Không xóa payment để giữ doanh thu

    // 0.2) Xóa cart liên quan trước
    $this->db->prepareDelete(
        "DELETE FROM `cart` WHERE account_id = ?",
        [$id],
        "i"
    );

    // 1) Xóa các role liên quan
    $this->db->prepareDelete(
        "DELETE FROM `accountrole` WHERE account_id = ?",
        [$id],
        "i"
    );

    // 2) Trước khi xóa account, set account_id trong payment về NULL
    $this->db->prepareUpdate(
        "UPDATE `payment` SET account_id = NULL WHERE account_id = ?",
        [$id],
        "i"
    );

    // 3) Xóa account
    return $this->db->prepareDelete(
        "DELETE FROM `account` WHERE id = ?",
        [$id],
        "i"
    );
}




    /** Đếm tổng số tài khoản */
    public function countAllUsers(): int {
        $result = $this->db->select("SELECT COUNT(*) AS cnt FROM `account`");
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)$row['cnt'];
        }
        return 0;
    }

    /** Lấy danh sách user có phân trang */
    public function getUsers(int $limit, int $offset): array {
        $sql    = "SELECT id, username, email, phone, createAt
                   FROM `account`
                   ORDER BY createAt DESC
                   LIMIT ?, ?";
        $result = $this->db->prepareSelect($sql, [$offset, $limit], "ii");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}