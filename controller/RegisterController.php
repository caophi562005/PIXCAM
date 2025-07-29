<?php
class RegisterController {
    public function index() {

        if (isset($_SESSION['username'])) {
             header("Location: index.php?controller=home&action=index");
            
        }

        $register_error = $_SESSION['register_error'] ?? '';
        unset($_SESSION['register_error']);
        
        include 'view/Register.php';
    }

    // Nếu sau này xử lý form đăng ký thì bạn có thể thêm action như:
    // public function handleRegister() {
    //     // Xử lý dữ liệu người dùng gửi lên
    // }

    public function handleRegister() {

    if (isset($_SESSION['username'])) {
             header("Location: index.php?controller=home&action=index");
            
    }

    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    
    // Kết nối Database
    require_once 'config/config.php';
    require_once 'lib/Database.php';
    $db = new Database();
    

    if (!preg_match('/^[A-Za-z0-9]{1,20}$/', $username)) {
        $_SESSION['register_error'] = "Tên đăng nhập không hợp lệ!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    

    // Kiểm tra username  đã tồn tại
    $checkUsernameQuery = "SELECT id FROM account WHERE username = ?";
    $checkUsername = $db->prepareSelect($checkUsernameQuery, [$username], "s");

    if ($checkUsername) {
        $_SESSION['register_error'] = "Username đã tồn tại!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Email không hợp lệ!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    $checkEmailQuery = "SELECT id FROM account WHERE email = ?";
    $checkEmail = $db->prepareSelect($checkEmailQuery, [$email], "s");

    if ($checkEmail) {
        $_SESSION['register_error'] = "Email đã tồn tại!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    // Kiểm tra số điện thoại hợp lệ (10 hoặc 11 số)
    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $_SESSION['register_error'] = "Số điện thoại không hợp lệ!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    $checkPhoneQuery = "SELECT id FROM account WHERE phone = ?";
    $checkPhone = $db->prepareSelect($checkPhoneQuery, [$phone], "s");

    if ($checkPhone) {
        $_SESSION['register_error'] = "Số điện thoại đã tồn tại!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    // Kiểm tra mật khẩu khớp
    if ($password !== $confirmPassword) {
       $_SESSION['register_error'] = "Mật khẩu không khớp!";
        header("Location: index.php?controller=register&action=index");
        exit;
        return;
    }

    // Hash mật khẩu
    $hashPassword = password_hash($password, PASSWORD_BCRYPT);

    

    // Thêm người dùng mới
    $insertQueryAccount = "INSERT INTO account (username, email, phone, passwordHash) VALUES (?, ?, ?, ?)";
    $insertAccount = $db->prepareInsert($insertQueryAccount, [$username, $email, $phone, $hashPassword], "ssss");

    $userId = $db->getInsertId();

    $roleId = 2; // id của role 'khach'
    $insertQueryRole = "INSERT INTO accountrole (account_id, role_id) VALUES (?, ?)";
    $insertRole = $db->prepareInsert($insertQueryRole, [$userId, $roleId], 'ii');

    if ($insertAccount && $insertRole) {
        $_SESSION['register_success'] = "Đăng kí thành công!";
        
    } else {
        $_SESSION['register_fail'] = "Đăng kí thất bại!";
        
    }
    header("Location: index.php?controller=login&action=index");
    exit;
    }
 

}