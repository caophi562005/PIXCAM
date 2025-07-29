<?php
require_once 'inc/header.php';
require_once __DIR__ . '/../models/FeedbackModel.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$feedbackModel = new FeedbackModel();
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/orderHistory.css">


<div class="wrapper">

    <h1>Lịch sử đơn hàng</h1>

    <?php if (!empty($orders)): ?>
    <table class="order-history-table">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Ngày đặt hàng</th>
                <th>Tổng cộng</th>
                <th>Phương thức</th>
                <th>Xem chi tiết</th>
                <th>Đánh giá</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <?php
            $rowspan = count($order['products']);
            $firstRow = true;
        ?>
            <?php foreach ($order['products'] as $product): ?>
            <tr>
                <?php if ($firstRow): ?>
                <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($order['payment_id']) ?></td>
                <td rowspan="<?= $rowspan ?>"><?= date("d/m/Y", strtotime($order['createAt'])) ?></td>
                <td rowspan="<?= $rowspan ?>"><?= number_format($order['finalTotal'], 0, '.', ',') ?> VNĐ</td>
                <td rowspan="<?= $rowspan ?>"><?= htmlspecialchars($order['paymentMethod']) ?></td>
                <td rowspan="<?= $rowspan ?>">
                    <a href="index.php?controller=order&action=detail&id=<?= $order['payment_id'] ?>">
                        Xem chi tiết
                    </a>
                </td>
                <?php endif; ?>

                <td>
                    <?php
                    $pid   = (int)$product['product_id'];
                    $payId = (int)$order['payment_id'];
                    $has   = $feedbackModel->hasFeedback($userId, $pid, $payId);
                    $edited = $has && $feedbackModel->hasEdited($userId, $pid, $payId);
                ?>
                    <?php if (!$has): ?>
                    <a href="index.php?controller=feedback&action=form&payment_id=<?= $payId ?>&product_id=<?= $pid ?>"
                        class="btn-rate">Đánh giá</a>
                    <?php elseif (!$edited): ?>
                    <a href="index.php?controller=feedback&action=form&payment_id=<?= $payId ?>&product_id=<?= $pid ?>"
                        class="btn-rate">Sửa đánh giá</a>
                    <?php else: ?>
                    <span class="btn-rate disabled" title="Bạn đã sửa đánh giá">Đã sửa</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php $firstRow = false; ?>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="index.php?controller=order&action=history&page=<?= $page - 1 ?>">« Trang trước</a>
        <?php endif; ?>

        <span>Trang <?= $page ?> / <?= $totalPages ?></span>

        <?php if ($page < $totalPages): ?>
        <a href="index.php?controller=order&action=history&page=<?= $page + 1 ?>">Trang sau »</a>
        <?php endif; ?>
    </div>

    <?php else: ?>
    <div class="empty-message">
        <p>Không có đơn hàng nào trong lịch sử của bạn.</p>
    </div>

    <?php endif; ?>

</div> <!-- ✅ đóng wrapper -->

<?php include 'inc/footer.php'; ?>