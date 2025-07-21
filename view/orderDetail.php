<?php include 'inc/header.php'; ?>
<link rel="stylesheet" href="view/css/orderDetail.css">

<h1>Chi tiết đơn hàng</h1>

<?php if (isset($orderDetails) && !empty($orderDetails)): ?>
<div class="order-detail-container">
    <h2>Mã đơn hàng: <?= htmlspecialchars($orderDetails['payment_id']) ?></h2>
    <p><strong>Ngày đặt hàng:</strong> <?= date("d/m/Y", strtotime($orderDetails['createAt'])) ?></p>
    <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($orderDetails['paymentMethod']) ?></p>
    <p><strong>Tạm tính:</strong> <?= number_format($orderDetails['total'], 0, '.', ',') ?> VNĐ</p>
    <p><strong>Giảm giá :</strong> -<?= number_format($orderDetails['discount_amount'], 0, '.', ',') ?> VNĐ</p>
    <p><strong>Tổng cộng:</strong> <?= number_format($orderDetails['finalTotal'], 0, '.', ',') ?> VNĐ</p>

    <!-- Thông tin giao hàng -->
    <div class="shipping-info">
        <h3>Thông tin giao hàng</h3>
        <p><strong>Họ và tên:</strong> <?= htmlspecialchars($orderDetails['name']) ?? 'Không có thông tin' ?></p>
        <p><strong>Địa chỉ giao hàng:</strong> <?= htmlspecialchars($orderDetails['address']) ?? 'Không có thông tin' ?>
        </p>
        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($orderDetails['phone']) ?? 'Không có thông tin' ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($orderDetails['email']) ?? 'Không có thông tin' ?></p>
    </div>

    <!-- Bảng Ghi Chú -->
    <h3>Ghi chú</h3>
    <table class="note-table">
        <thead>
            <tr>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="note-cell">
                    <?= isset($orderDetails['note']) && !empty($orderDetails['note']) ? htmlspecialchars($orderDetails['note']) : 'Không có ghi chú' ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Chi tiết sản phẩm -->
    <h3>Chi tiết sản phẩm</h3>
    <table class="order-detail-table">
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
                <th>Đánh giá</th>
            </tr>
        </thead>
        <tbody>
  <?php if (!empty($orderDetails['products'])): ?>
    <?php foreach ($orderDetails['products'] as $product): ?>
      <tr>
        <!-- Tên sản phẩm -->
        <td>
          <?= htmlspecialchars($product['productName']) ?>
        </td>
        <!-- Số lượng -->
        <td class="text-center">
          <?= intval($product['quantity']) ?>
        </td>
        <!-- Giá -->
        <td class="text-right">
          <?= number_format($product['price'], 0, '.', ',') ?> VNĐ
        </td>
        <!-- Thành tiền -->
        <td class="text-right">
          <?= number_format($product['productTotal'], 0, '.', ',') ?> VNĐ
        </td>
        <!-- Nút Đánh giá -->
        <td class="text-center">
          <a
            href="index.php?controller=feedback&action=form&payment_id=<?= $orderDetails['payment_id'] ?>&product_id=<?= $product['product_id'] ?>"
            class="btn-rate"
          >
            Đánh giá
          </a>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php else: ?>
    <tr>
      <td colspan="5" class="text-center">Không có sản phẩm trong đơn hàng này.</td>
    </tr>
  <?php endif; ?>
</tbody>

    </table>
</div>

<div class="return-home">
    <button onclick="window.location.href='index.php?controller=order&action=history'">Quay lại lịch sử đơn
        hàng</button>
</div>

<?php else: ?>
<p>Không tìm thấy chi tiết đơn hàng.</p>
<?php endif; ?>

<?php include 'inc/footer.php'; ?>