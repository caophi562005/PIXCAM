<?php include 'inc/header.php'; ?>

<div class="content">
    <h1 class="inf_title_paycart">Thông tin thanh toán</h1>

    <!-- Hiển thị lỗi (nếu có) -->
    <?php if (!empty($errors)): ?>
    <div class="message error">
        <?php foreach ($errors as $err): ?>
        <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="index.php?controller=pay&action=confirm" method="post">
        <div class="contentPayCart">
            <!-- Thông tin người dùng -->
            <div class="infPay">
                <div class="box_infPay">
                    <p class="lable_infPay"><strong>Họ và tên</strong></p>
                    <input name="name" type="text" class="input_box_infPay"
                        value="<?= htmlspecialchars($account['username'] ?? '') ?>" required minlength="2"
                        title="Vui lòng nhập họ và tên (ít nhất 2 ký tự)" />
                </div>

                <div class="box_sdt">
                    <div class="item_infPays">
                        <p><strong>Số điện thoại</strong></p>
                        <input name="phone" type="tel" class="input_box_sdt"
                            value="<?= htmlspecialchars($account['phone'] ?? '') ?>" required pattern="\d{9,11}"
                            title="Vui lòng nhập số điện thoại (9–11 chữ số)" />
                    </div>
                    <div class="item_infPays">
                        <p><strong>Địa chỉ email</strong></p>
                        <input name="email" type="email" class="input_box_sdt"
                            value="<?= htmlspecialchars($account['email'] ?? '') ?>" required
                            title="Vui lòng nhập email hợp lệ" />
                    </div>
                </div>

                <div class="box_infPay">
                    <p class="lable_infPay"><strong>Địa chỉ giao hàng</strong></p>
                    <input name="address" type="text" class="input_box_sdt" required minlength="5"
                        title="Địa chỉ phải có ít nhất 5 ký tự" />
                </div>

                <div class="box_infPay">
                    <p class="lable_infPay"><strong>Ghi chú (tùy chọn)</strong></p>
                    <textarea name="note" class="input_box_note"></textarea>
                </div>
            </div>

            <!-- Thông tin đơn hàng -->
            <div class="detailPayCart">
                <div class="title_detail_cart">
                    <p class="name_detail_cart">ĐƠN HÀNG CỦA BẠN</p>
                </div>
                <div class="content_detailPayCart">
                    <div class="box_content_detailPayCart">
                        <p class="lable_detailPayCart"><strong>Sản phẩm</strong></p>
                        <p class="lable_detailPayCart"><strong>Thành tiền</strong></p>
                    </div>

                    <?php foreach ($items as $it): ?>
                    <div class="box_content_detailPayCart">
                        <p class="lable_detailPayCart"><?= htmlspecialchars($it['product_name']) ?></p>
                        <p class="lable_detailPayCart"><?= number_format($it['total'], 0, '.', ',') ?> VNĐ</p>
                    </div>
                    <?php endforeach; ?>

                    <div class="box_content_detailPayCart">
                        <p class="lable_content">Tạm tính</p>
                        <p class="price_title_box"><?= number_format($subtotal, 0, '.', ',') ?> VNĐ</p>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="box_content_detailPayCart">
                        <p class="lable_detailPayCart">Giảm <?= intval($coupon['percent']) ?>%</p>
                        <p class="lable_detailPayCart">-<?= number_format($discount, 0, '.', ',') ?> VNĐ</p>
                    </div>
                    <?php endif; ?>

                    <div class="box_content_detailPayCart">
                        <p class="lable_detailPayCart">Tổng cộng</p>
                        <p class="lable_detailPayCart"><?= number_format($finalTotal, 0, '.', ',') ?> VNĐ</p>
                    </div>

                    <!-- Payment Method -->
                    <div class="checked_detailPayCart">
                        <input type="radio" name="paymentMethod" value="Tiền mặt khi nhận hàng" checked />
                        <p>Tiền mặt khi nhận hàng</p>
                    </div>
                    <div class="checked_detailPayCart">
                        <input type="radio" name="paymentMethod" value="Chuyển khoản" />
                        <p>Chuyển khoản</p>
                    </div>

                    <!-- Actions -->
                    <div class="box_content_detailPayCart">
                        <div class="btn_back_detailPayCart">
                            <a href="index.php?controller=cart&action=index">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Quay lại giỏ hàng</span>
                            </a>
                        </div>
                        <button type="submit" class="btn_payOnline">
                            <p>Đặt hàng</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include 'inc/footer.php'; ?>