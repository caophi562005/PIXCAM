<?php
require_once __DIR__ . '/../models/AccountModel.php';
require_once __DIR__ . '/../lib/database.php';

class ProfileController {
    private $model;

    public function __construct() {
        // Kiểm tra session đã được khởi tạo chưa
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Nếu chưa login, chuyển hướng
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_required'] = "Vui lòng đăng nhập trước.";
            header('Location: index.php?controller=login&action=index');
            exit;
        }

        $this->model = new AccountModel();
    }

    /**
     * Hiển thị form chỉnh sửa hồ sơ
     */
    public function edit() {
        $id   = $_SESSION['user_id'];
        $user = $this->model->getById($id);

        $success = $_SESSION['success'] ?? '';
        $errors  = $_SESSION['errors']  ?? [];
        unset($_SESSION['success'], $_SESSION['errors']);

        include __DIR__ . '/../view/profile.php';
    }

    /**
     * Xử lý cập nhật hồ sơ và mật khẩu
     */
    public function update() {
        // Session đã khởi
        $id          = $_SESSION['user_id'];
        $username    = trim($_POST['username'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $errors = [];

        // Validate thông tin cơ bản
        if ($username === '' || $email === '' || $phone === '') {
            $errors[] = 'Username, email và phone không được để trống.';
        }
        

        if (!preg_match('/^[A-Za-z0-9]{1,20}$/', $username)) {
            $errors[] = 'Tên đăng nhập không hợp lệ!';  // Validate Username
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
    }

        


        $db = new Database;
        // Kiểm tra username hoặc email đã tồn tại
        $checkUsernameQuery = "SELECT id FROM account WHERE username = ? AND id != ?";
        $checkUsername = $db->prepareSelect($checkUsernameQuery, [$username,$id], "si");

        if ($checkUsername) {
            $errors[] = 'Username đã tồn tại!'; //Kiểm tra Username trùng lặp
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';  // Validate Email
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
        }

        $checkEmailQuery = "SELECT id FROM account WHERE email = ? AND id != ?";
        $checkEmail = $db->prepareSelect($checkEmailQuery, [$email, $id], "si");

        if ($checkEmail) {
            $errors[] = 'Email đã tồn tại!';  // Kiểm tra email trùng lặp
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
        }

        // Kiểm tra số điện thoại hợp lệ (10 hoặc 11 số)
        if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
            $errors[] = 'Số điện thoại không hợp lệ!';  // Validate số điện thoại
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
        }

        $checkPhoneQuery = "SELECT id FROM account WHERE phone = ? AND id != ?";
        $checkPhone = $db->prepareSelect($checkPhoneQuery, [$phone, $id], "si");

        if ($checkPhone) {
            $errors[] = 'Số điện thoại đã tồn tại!'; // Kiểm tra số điện thoại trùng lặp
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=profile&action=edit');
            exit;
            return;
        }

        // Kiểm tra đổi mật khẩu
        if ($newPassword !== '') {
            $db = new Database();
            $row = $db->fetchOne(
                "SELECT passwordHash FROM account WHERE id = ?",
                [$id], 'i'
            );
            if (!$row || !password_verify($oldPassword, $row['passwordHash'])) {
                $errors[] = 'Mật khẩu cũ không đúng.';
                $_SESSION['errors'] = $errors;
                header('Location: index.php?controller=profile&action=edit');
                exit;
                return;
            }
        }

        

        // Cập nhật thông tin
        $this->model->updateInfo($id, $username, $email, $phone);

        // Cập nhật mật khẩu nếu có
        if ($newPassword !== '') {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $this->model->updatePassword($id, $hash);
        }

        $_SESSION['success'] = 'Cập nhật thành công.';
        header('Location: index.php?controller=profile&action=edit');
        exit;
    }
}