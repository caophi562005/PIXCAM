<?php
// Format.php
class Format {

    // Format ngày tháng
    public function formatDate($date) {
        return date('F j, Y, g:i a', strtotime($date));
    }

    // Rút gọn văn bản
    public function textShorten($text, $limit = 400) {
        if (strlen($text) <= $limit) return $text;
        $text = substr($text, 0, strpos($text, ' ', $limit));
        return $text . '...';
    }

    // Kiểm tra và làm sạch dữ liệu đầu vào
    public function validation($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Format tiêu đề từ đường dẫn hiện tại
    public function title() {
        $path = $_SERVER['SCRIPT_FILENAME'];
        $title = basename($path, '.php');
        if ($title == 'index') {
            $title = 'home';
        } elseif ($title == 'contact') {
            $title = 'contact';
        }
        return ucfirst($title);
    }

  // Format tiền tệ (VND)
public function formatCurrency($amount) {
    // Ép về số nguyên để chắc chắn không có phần thập phân
    $amount = intval($amount);

    // number_format($amount, 0, ',', '.') 
    //   – 0: số chữ số phần thập phân (ở đây là 0)
    //   – ',' : dấu phân cách phần thập phân (không dùng vì 0 decimal)
    //   – '.' : dấu phân cách hàng nghìn
   return number_format($amount, 0, '.', ',') . ' VNĐ';

}


    // In hoa chữ cái đầu tiên của chuỗi
    public function ucFirst($string) {
        return ucfirst($string);
    }

    // In hoa từng chữ trong chuỗi
    public function ucWords($string) {
        return ucwords($string);
    }

    // In thường chuỗi
    public function toLower($string) {
        return strtolower($string);
    }

    // In hoa chuỗi
    public function toUpper($string) {
        return strtoupper($string);
    }
    // Tính giá sau khi giảm (giảm theo %)
public function getDiscountedPrice($price, $saleBadge) {
    if (!is_numeric($price) || $price <= 0 || empty($saleBadge)) {
        return $price;
    }

    $percent = floatval(str_replace('%', '', $saleBadge)) / 100;
    $newPrice = $price * (1 - $percent);
    return $newPrice;
}
// Vừa tính giảm giá vừa định dạng lại giá tiền
public function getDiscountedPriceFormatted($price, $saleBadge) {
    $discounted = $this->getDiscountedPrice($price, $saleBadge);
    return $this->formatCurrency($discounted);
}

}
?>