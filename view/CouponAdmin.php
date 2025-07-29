<?php include 'inc/header.php'; ?>

<link href="<?php echo BASE_URL; ?>view/css/CouponAdmin.css" rel="stylesheet" />
<div class="wrapper">
    <section class="coupon-section">
        <div class="coupon-container">
            <?php if (!empty($_SESSION['coupon_error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['coupon_error']; unset($_SESSION['coupon_error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['coupon_success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['coupon_success']; unset($_SESSION['coupon_success']); ?>
            </div>
            <?php endif; ?>

            <a href="index.php" class="btn-back mb-3">← Về trang chủ</a>
            <!-- Thêm mã -->
            <h2 class="coupon-title">Thêm Mã Giảm Giá</h2>
            <form action="index.php?controller=CouponAdmin&action=handle" method="post" class="coupon-form-inline mb-5">
                <input type="hidden" name="_action" value="add" />
                <div class="form-group">
                    <label for="code">Mã Coupon</label>
                    <input id="code" name="code" type="text" placeholder="VD: VIP001" required />
                </div>
                <div class="form-group">
                    <label for="percent">Phần trăm giảm (%)</label>
                    <input id="percent" name="percent" type="number" placeholder="Nhập %" required min="1" max="100" />
                </div>
                <div class="form-group">
                    <label for="min_order">Đơn tối thiểu (VNĐ)</label>
                    <input id="min_order" name="min_order" type="number" placeholder="Ví dụ 200000" value="0" min="0" />
                </div>
                <button type="submit" class="btn-action btn-primary">Thêm Coupon</button>
            </form>

            <!-- Danh sách -->
            <h2 class="coupon-title">Danh sách Mã Giảm Giá</h2>
            <form action="index.php?controller=CouponAdmin&action=handle" method="post">
                <div class="coupon-list-wrapper mb-3">
                    <ul class="coupon-list">
                        <?php if (empty($coupons)): ?>
                        <li class="coupon-item" style="color:#666;">Không có coupon nào</li>
                        <?php else: foreach ($coupons as $c): ?>
                        <li class="coupon-item">
                            <input type="checkbox" name="selectedCodes[]" value="<?=htmlspecialchars($c['code'])?>"
                                class="coupon-checkbox" />
                            <span class="coupon-code">
                                <?=htmlspecialchars($c['code'])?> — <?=intval($c['percent'])?>%
                                (min <?=number_format($c['min_order'],0,',','.')?>₫)
                                <?php if ($c['expiration']): ?>
                                <small>Hết hạn: <?=date('d/m/Y H:i',strtotime($c['expiration']))?></small>
                                <?php endif; ?>
                            </span>
                        </li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>

                <div class="form-group mb-3">
                    <label for="scheduledTime">Thiết lập hạn thời gian</label><br />
                    <input id="scheduledTime" type="datetime-local" name="scheduledTime" />
                </div>

                <div class="coupon-actions">
                    <button type="submit" name="_action" value="delete" class="btn-action btn-danger">Xóa</button>
                    <button type="submit" name="_action" value="schedule" class="btn-action btn-primary">Cập
                        nhật</button>
                </div>
            </form>
        </div>
    </section>
</div>
<?php include 'inc/footer.php'; ?>