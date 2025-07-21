<?php include 'inc/header.php'; ?>

<?php
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>
<link href="view/css/feedbackfrom.css" rel="stylesheet" />

<section class="feedback-section">
    <div class="feedback-card">
        <h1 class="feedback-title">
            <?= empty($feedback) ? 'Đánh giá sản phẩm' : 'Sửa đánh giá' ?>
        </h1>

        <?php if ($flashError): ?>
        <div class="feedback-message error"><?= $flashError ?></div>
        <?php elseif ($flashSuccess): ?>
        <div class="feedback-message success"><?= $flashSuccess ?></div>
        <?php endif; ?>

        <form action="index.php?controller=feedback&action=submit" method="post" class="feedback-form">
            <input type="hidden" name="payment_id" value="<?= $paymentId ?>">
            <input type="hidden" name="product_id" value="<?= $productId ?>">

            <?php
                $currRating  = $feedback['rating'] ?? 5;
                $currComment = htmlspecialchars($feedback['comment'] ?? '', ENT_QUOTES);
            ?>

            <div class="field-group">
                <label><strong>Sản phẩm:</strong></label>
                <p><?= htmlspecialchars($productName) ?></p>

                <?php if (!empty($productImg)): ?>
                <img src="<?= htmlspecialchars($productImg) ?>" alt="<?= htmlspecialchars($productName) ?>"
                    class="product-thumbnail">
                <?php endif; ?>

                <label><strong>Đánh giá sao:</strong></label>
                <select name="rating" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>" <?= $i == $currRating ? 'selected' : '' ?>>
                        <?= $i ?> sao
                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="field-group">
                <label><strong>Bình luận:</strong></label>
                <textarea name="comment" rows="4"><?= $currComment ?></textarea>
            </div>

            <button type="submit" class="btn-feedback">
                <?= empty($feedback) ? 'Gửi đánh giá' : 'Cập nhật đánh giá' ?>
            </button>
        </form>
    </div>
</section>

<?php include 'inc/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var select = document.querySelector('.field-group select');
    select?.addEventListener('change', function() {
        // Có thể thêm hiệu ứng nếu cần
    });

    const successMsg = document.querySelector('.feedback-message.success');
    if (successMsg) {
        setTimeout(() => {
            window.location.href = "index.php?controller=order&action=history";
        }, 1500);
    }
});
</script>