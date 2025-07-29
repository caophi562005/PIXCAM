<?php
require_once 'lib/Database.php';
class LoginController {
    public function index() {

        if (isset($_SESSION['username'])) {
             header("Location: index.php?controller=home&action=index");
            
        }

        if (isset($_SESSION['login_required'])) {
            $login_required_message = $_SESSION['login_required'];
                // Xóa session để thông báo chỉ hiển thị 1 lần
                unset($_SESSION['login_required']);
                echo "<script>window.onload = function() { alert('$login_required_message'); }</script>";
        }

        
        if (isset($_SESSION['register_success'])) {
            $register_message = $_SESSION['register_success'];
            // Xóa session để thông báo chỉ hiển thị 1 lần
            
            echo "<script>window.onload = function() { alert('$register_message'); }</script>";
             unset($_SESSION['register_success']);
        }

        $old_username = $_SESSION['old_username'] ?? '';
        $login_error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['old_username'],$_SESSION['login_error']);

        include 'view/Login.php';
    }

    
    

    public function handleLogin() {
        if (isset($_SESSION['username'])) {
             header("Location: index.php?controller=home&action=index");
            
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // Kiểm tra dữ liệu đầu vào
        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Vui lòng điền đầy đủ thông tin.";
        }

        // Kết nối DB
        
        
        $db = new Database();

        // Truy vấn thông tin tài khoản
        $queryAccount = "SELECT * FROM account WHERE username = ?";
        $result = $db->prepareSelect($queryAccount, [$username], 's');

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Kiểm tra mật khẩu
            if (password_verify($password, $user['passwordHash'])) {
                
                // Lưu thông tin vào session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['login_success'] = "Đăng nhập thành công!";

                $userId = $user['id'];
                $roleQuery = "
                                SELECT r.name 
                                FROM role r 
                                INNER JOIN accountrole ar ON r.id = ar.role_id 
                                WHERE ar.account_id = ?
                            ";
                $roleResult = $db->prepareSelect($roleQuery, [$userId], 'i');
                if ($roleResult && $roleResult->num_rows > 0) {
                    $roleRow = $roleResult->fetch_assoc();
                    $_SESSION['role'] = $roleRow['name'];
                }
                
                
                header("Location: index.php?controller=home&action=index ");
                exit;
            } else {
                $_SESSION['old_username'] = $username;
                $_SESSION['login_error'] = "Tài khoản hoặc mật khẩu không đúng!";
            }
        } else {
            $_SESSION['old_username'] = $username;
           $_SESSION['login_error'] = "Tài khoản hoặc mật khẩu không đúng!";
        }
        // Quay lại trang đăng nhập nhưng giữ lại lỗi
        header("Location: index.php?controller=login&action=index");
        exit;
    }
    public function logout() {
    session_start();
    session_unset();
    session_destroy();

    session_start();
     $_SESSION['logout_success'] = "Đăng xuất thành công.";
    header("Location: index.php?controller=home&action=index");
    exit;
}
}

