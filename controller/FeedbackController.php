<?php
require_once __DIR__ . '/../models/FeedbackModel.php';
require_once __DIR__ . '/../models/ProductModel.php';

class FeedbackController {
    public function form() {
        if (!isset($_SESSION['user_id'], $_GET['payment_id'], $_GET['product_id'])) {
            header("Location: index.php");
            exit;
        }

        $accountId = (int)$_SESSION['user_id'];
        $paymentId = (int)$_GET['payment_id'];
        $productId = (int)$_GET['product_id'];

        $fm = new FeedbackModel();
        $pm = new ProductModel();

        $feedback = $fm->getFeedback($accountId, $productId, $paymentId);
        $product = $pm->getProductById($productId);

        $productName = $product['name'] ?? 'Sản phẩm không tồn tại';
        $productImg  = $product['imgURL_1'] ?? null;

        if (!empty($feedback) && (int)$feedback['is_edited'] === 1) {
            include __DIR__ . '/../view/FeedbackReadOnly.php';
        } else {
            include __DIR__ . '/../view/FeedbackForm.php';
        }
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit;
        }

        $accountId = $_SESSION['user_id'];
        $paymentId = (int)$_POST['payment_id'];
        $productId = (int)$_POST['product_id'];
        $rating    = (int)$_POST['rating'];
        $comment   = trim($_POST['comment']);

        $fm = new FeedbackModel();
        $pm = new ProductModel();

        if ($fm->hasFeedback($accountId, $productId, $paymentId)) {
            if ($fm->hasEdited($accountId, $productId, $paymentId)) {
                $_SESSION['flash_error'] = "Bạn chỉ được sửa đánh giá 1 lần thôi.";
            } else {
                $fm->update($accountId, $productId, $paymentId, $rating, $comment);
                $_SESSION['flash_success'] = "Bạn đã sửa đánh giá thành công.";
            }
        } else {
            $fm->add($accountId, $productId, $paymentId, $rating, $comment);
            $_SESSION['flash_success'] = "Cảm ơn bạn đã đánh giá!";
        }

        $feedback = $fm->getFeedback($accountId, $productId, $paymentId);
        $product = $pm->getProductById($productId);
        $productName = $product['name'] ?? 'Sản phẩm không tồn tại';
        $productImg  = $product['imgURL_1'] ?? null;

        include __DIR__ . '/../view/FeedbackForm.php';
    }
}