<?php include 'inc/header.php'; ?>

<link href="view/css/feedbackReadonly.css" rel="stylesheet" />

<section class="feedback-section">
    <div class="feedback-card">
        <h1 class="feedback-title">Nội dung đánh giá của bạn</h1>

        <div class="feedback-content">
            <div class="field-group">
                <label>Số sao:</label>
                <div class="stars">
                    <?php
            // In dấu sao vàng
            for ($i = 1; $i <= 5; $i++) {
              if ($i <= $feedback['rating']) {
                echo '<span>&#9733;</span>'; // sao filled
              } else {
                echo '<span style="color:#ccc">&#9733;</span>'; // sao trống
              }
            }
          ?>
                </div>
            </div>

            <div class="field-group">
                <label>Bình luận:</label>
                <div><?= nl2br(htmlspecialchars($feedback['comment'], ENT_QUOTES)) ?></div>
            </div>

            <div class="field-group">
                <label>Ngày gửi:</label>
                <div>
                    <?= date('d/m/Y H:i', strtotime($feedback['updated_at'] ?? $feedback['created_at'])) ?>
                </div>
            </div>
        </div>

        <a href="index.php?controller=order&action=history" class="btn-feedback">
            Quay về lịch sử đơn hàng
        </a>
    </div>
</section>

<?php include 'inc/footer.php'; ?>