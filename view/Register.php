<?php 
include 'inc/header.php'; ?>
<link href="view/css/Register2.css" rel="stylesheet" />

<div class="register-wrapper">
    <div class="register-box">
        <h2>Đăng ký</h2>
        <form action="index.php?controller=register&action=handleRegister" method="POST">
            <input type="text" id="username" name="username" placeholder="Tên đăng nhập" required />
            <small id="usernameError" class="usernameError"></small>
            <input type="email" name="email" placeholder="Email" required />
            <input type="text" name="phone" placeholder="Số điện thoại" required />
            <input type="password" name="password" placeholder="Mật khẩu" required />
            <input type="password" id="cf_pw" name="confirm_password" placeholder="Nhập lại mật khẩu" required />
            <div class="error-placeholder">
                <?php if (!empty($register_error)): ?>
                <span class="error-message"><?php echo $register_error; ?></span>
                <?php endif; ?>
            </div>
            <button type="submit">Đăng ký</button>
        </form>
        <div class="login-link">
            Đã có tài khoản? <a href="index.php?controller=login&action=index">Đăng nhập</a>
        </div>
    </div>
</div>

<script>
document.getElementById('username').addEventListener('input', function () {
    const username = this.value;
    const errorField = document.getElementById('usernameError');

    // Regex chỉ cho phép chữ không dấu và số, tối đa 20 ký tự
    const regex = /^[A-Za-z0-9]{0,20}$/;

    if (!regex.test(username)) {
        errorField.textContent = "Chỉ được nhập chữ không dấu và số, tối đa 20 ký tự.";
        this.style.borderColor = '#dc3545';
    } else {
        errorField.textContent = "";
        this.style.borderColor = "#ccc";
    }
});
</script>

<?php include 'inc/footer.php'; ?>