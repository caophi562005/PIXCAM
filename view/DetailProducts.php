<?php
include 'inc/header.php';
include_once 'helpers/format.php';
$fm = new Format();
?>
<link href="view/css/detailProduct.css" rel="stylesheet" />

<div class="content">
    <div class="content_detailProduct">
        <!-- Phần hình ảnh chính -->
        <div class="img_product">
            <!-- Ảnh lớn (main), gán id="main-image" để thay đổi src khi click thumbnail -->
            <img id="main-image" src="<?php echo htmlspecialchars($product['imgURL_1']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>" class="image_shirt" />

            <div class="image_detail_product">
                <!-- Thumbnail 1: Ảnh gốc (imgURL_1) để quay về khi nhấn -->
                <img src="<?php echo htmlspecialchars($product['imgURL_1']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>" class="image_shirt_detail"
                    style="cursor: pointer;" onclick="document.getElementById('main-image').src = this.src;" />

                <!-- Thumbnail 2 -->
                <?php if (!empty($product['imgURL_2'])): ?>
                <img src="<?php echo htmlspecialchars($product['imgURL_2']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>" class="image_shirt_detail"
                    style="cursor: pointer;" onclick="document.getElementById('main-image').src = this.src;" />
                <?php endif; ?>

                <!-- Thumbnail 3 -->
                <?php if (!empty($product['imgURL_3'])): ?>
                <img src="<?php echo htmlspecialchars($product['imgURL_3']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>" class="image_shirt_detail"
                    style="cursor: pointer;" onclick="document.getElementById('main-image').src = this.src;" />
                <?php endif; ?>

                <!-- Thumbnail 4 -->
                <?php if (!empty($product['imgURL_4'])): ?>
                <img src="<?php echo htmlspecialchars($product['imgURL_4']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>" class="image_shirt_detail"
                    style="cursor: pointer;" onclick="document.getElementById('main-image').src = this.src;" />
                <?php endif; ?>
            </div>
        </div>

        <!-- Phần thông tin chi tiết sản phẩm -->
        <div class="inf_product">
            <!-- Tên sản phẩm -->
            <h2 class="title_inf_products">
                <?php echo htmlspecialchars($product['name']); ?>
            </h2>

            <!-- Giá sản phẩm -->
            <p class="price_inf_products">
                <?php if (isset($product['price_sale'])): ?>
                <!-- Giá gốc gạch ngang -->
                <span class="price-original">
                    <?php echo $fm->formatCurrency($product['price']); ?>
                </span>
                &nbsp;
                <!-- Giá đã giảm -->
                <span class="price-sale">
                    <?php echo $fm->formatCurrency($product['price_sale']); ?>
                </span>
                &nbsp;
                <!-- Phần trăm giảm -->
                <span
                    style="background-color: #ff4d4f; color: #fff; padding: 2px 6px; font-size: 0.9rem; border-radius: 3px;">
                    Giảm <?php echo intval($product['discount_percent']); ?>%
                </span>
                <?php else: ?>
                <!-- Chỉ hiển thị giá gốc nếu không có sale -->
                <span>
                    <?php echo $fm->formatCurrency($product['price']); ?>
                </span>
                <?php endif; ?>
            </p>

            <!-- Tình trạng -->
            <p class="status_inf_products">
                Tình trạng:
                <?php if (intval($product['quantity']) > 0): ?>
                <span class="status_color_inf">còn hàng</span>
                <?php else: ?>
                <span class="status_color_inf" style="color: red;">hết hàng</span>
                <?php endif; ?>
            </p>

            <!-- === Phần MÀU SẮC: chỉ hiển thị nếu có màu === -->
            <?php if (!empty($product['colors'])): ?>
            <p class="color_inf_products">Màu sắc:</p>
            <div class="item_box_color">
                <?php foreach ($product['colors'] as $index => $colorName):
                        $cleanName = trim($colorName);
                        $colorId   = 'color_' . $index;
                        ?>
                <div class="color-item">
                    <input type="radio" id="<?php echo $colorId; ?>" name="selected_color"
                        value="<?php echo htmlspecialchars($cleanName); ?>"
                        <?php echo ($index === 0) ? 'checked' : ''; ?> />
                    <label for="<?php echo $colorId; ?>">
                        <span class="color-swatch"></span>
                        <span class="color-name"><?php echo htmlspecialchars($cleanName); ?></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CSS gán background-color và border-color tương ứng khi được chọn -->
            <style>
            <?php foreach ($product['colors'] as $index=> $colorName): $cleanName=trim($colorName);
            $isValidHex=preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $cleanName);
            $isCSSName=preg_match('/^[a-zA-Z]+$/', $cleanName);
            // Nếu không phải hex hoặc CSS name, gán màu mặc định
            $bg=($isValidHex || $isCSSName) ? $cleanName : '#757575';
            ?>#color_<?php echo $index;

            ?>:checked+label .color-swatch {
                background-color: <?php echo htmlspecialchars($bg);
                ?>;
                border-color: <?php echo htmlspecialchars($bg);
                ?>;
            }

            <?php endforeach;
            ?>
            </style>
            <?php endif; ?>
            <!-- === Kết thúc phần MÀU SẮC === -->

            <!-- Kích thước -->
            <p class="size_inf_products">Kích thước:</p>
            <div class="box_option_size">
                <?php if (!empty($product['sizes'])): ?>
                <?php foreach ($product['sizes'] as $idx => $size):
                        $sizeId = 'size_' . $idx;
                        ?>
                <div class="size-item">
                    <input type="radio" id="<?php echo $sizeId; ?>" name="selected_size"
                        value="<?php echo htmlspecialchars($size['name']); ?>"
                        <?php echo ($idx === 0) ? 'checked' : ''; ?> />
                    <label for="<?php echo $sizeId; ?>">
                        <?php echo htmlspecialchars($size['name']); ?>
                    </label>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <span>Không có kích thước</span>
                <?php endif; ?>
            </div>

            <!-- Số lượng và nút Thêm vào giỏ hàng -->
            <p class="quantity_inf_products">Số lượng:</p>
            <div class="quantity_box">
                <div class="detail_quatity">
                    <!-- Nút giảm (−) bên trái, input ở giữa, nút tăng (+) bên phải -->
                    <button class="totalProducts" onclick="changeQty(-1)">−</button>
                    <input type="text" id="input-qty" value="1" readonly />
                    <button class="totalProducts" onclick="changeQty(1)">+</button>
                </div>
                <form id="form-add-cart" action="index.php?controller=cart&action=add" method="post">
                    <input type="hidden" name="product_id" value="<?php echo intval($product['id']); ?>">
                    <!-- màu sắc -->
                    <input type="hidden" name="color" id="input-color" value="">
                    <!-- size (có thể không có) -->
                    <input type="hidden" name="size" id="input-size" value="">
                    <!-- số lượng -->
                    <input type="hidden" name="qty" id="input-qty-hidden" value="1">
                    <!-- Nút thêm -->
                    <?php if (intval($product['quantity']) > 0): ?>
                    <button type="submit" id="btn-add-cart" class="btn_quantity_box">Thêm vào giỏ hàng</button>
                    <?php else: ?>
                    <button type="button" class="btn_quantity_box" disabled
                        style="background-color: #ccc; cursor: not-allowed;">
                        Hết hàng
                    </button>
                    <?php endif; ?>

                </form>
                <div id="cart-alert" style="margin-top:20px; color: green; font-weight: bold;"></div>
            </div>

            <!-- Chi tiết mô tả sản phẩm -->
            <div class="inf_detailProducts">
                <p class="title_detai_products">Chi tiết sản phẩm</p>
                <ul class="box_detail_products_inf">
                    <?php
                    // Tách nội dung detail theo dòng mới
                    $details = explode("\n", trim($product['detail']));
                    foreach ($details as $line):
                        $line = trim($line);
                        if ($line !== ''):
                            ?>
                    <li><?php echo htmlspecialchars($line); ?></li>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
    </div>



    <div class="feedback-container">
        <section class="product-feedback-section">
            <h3>ĐÁNH GIÁ SẢN PHẨM</h3>

            <?php
  $feedbacks = $feedbacks ?? [];
  $stats     = $stats     ?? ['avg' => 0, 'total' => 0];

  $totalReviews = (int)$stats['total'];
  $sumStars     = round($totalReviews * (float)$stats['avg']); // <- đã sửa

  $avg = $totalReviews
      ? number_format($sumStars / $totalReviews, 1)
      : '0.0';

  $filledStars = round($avg);
  $emptyStars  = 5 - $filledStars;
?>



            <!-- summary -->
            <div class="feedback-summary">
                <div class="avg-score"><?= $avg ?></div>
                <div class="stars">
                    <?= str_repeat('★', $filledStars) . str_repeat('☆', $emptyStars) ?>
                </div>
                <div class="total">(<?= $totalReviews ?> đánh giá)</div>
                <div class="sum-stars">Tổng sao: <?= $sumStars ?></div>
            </div>

            <!-- form (nếu có quyền đánh giá) -->
            <?php if (!empty($canReview)): ?>
            <div class="feedback-form">
                <!-- form đánh giá (giữ nguyên) -->
            </div>
            <?php endif; ?>

            <!-- danh sách đánh giá -->
            <?php if (!empty($feedbacks)): ?>
            <ul class="feedback-list">
                <?php foreach ($feedbacks as $f): ?>
                <li class="feedback-item">
                    <div class="feedback-header">
                        <div class="header-info">
                            <strong><?= htmlspecialchars($f['username']) ?></strong>
                            <div class="stars">
                                <?= str_repeat('★', $f['rating']) . str_repeat('☆', 5 - $f['rating']) ?>
                            </div>
                            <small class="date"><?= date('d/m/Y', strtotime($f['created_at'])) ?></small>
                        </div>
                    </div>

                    <?php if (trim($f['comment'])): ?>
                    <p class="comment"><?= nl2br(htmlspecialchars($f['comment'])) ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <!-- Phân trang feedback -->
            <?php if ($totalPages > 1): ?>
            <div class="feedback-pagination">
                <?php
// Vùng hiển thị trang
$range = 2;
$qsBase = $_GET;

// Mũi tên prev (luôn hiện)
$qsBase['fb_page'] = max(1, $currentPage - 1);
echo '<a href="?' . http_build_query($qsBase) . '" class="' . ($currentPage == 1 ? 'disabled' : '') . '">&lt;</a>';

// Trang 1
$qsBase['fb_page'] = 1;
echo '<a href="?' . http_build_query($qsBase) . '"' . ($currentPage == 1 ? ' class="active"' : '') . '>1</a>';

// Dấu ...
if ($currentPage - $range > 2) {
    echo '<span class="dot">...</span>';
}

// Các trang giữa
$start = max(2, $currentPage - $range);
$end = min($totalPages - 1, $currentPage + $range);

for ($i = $start; $i <= $end; $i++) {
    $qsBase['fb_page'] = $i;
    $cls = ($i == $currentPage) ? ' class="active"' : '';
    echo '<a href="?' . http_build_query($qsBase) . '"' . $cls . '>' . $i . '</a>';
}

// Dấu ...
if ($currentPage + $range < $totalPages - 1) {
    echo '<span class="dot">...</span>';
}

// Trang cuối (nếu > 1)
if ($totalPages > 1) {
    $qsBase['fb_page'] = $totalPages;
    echo '<a href="?' . http_build_query($qsBase) . '"' . ($currentPage == $totalPages ? ' class="active"' : '') . '>' . $totalPages . '</a>';
}

// Mũi tên next (luôn hiện)
$qsBase['fb_page'] = min($totalPages, $currentPage + 1);
echo '<a href="?' . http_build_query($qsBase) . '" class="' . ($currentPage == $totalPages ? 'disabled' : '') . '">&gt;</a>';
?>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <p class="no-feedback">Chưa có đánh giá nào.</p>
            <?php endif; ?>

        </section>
    </div>


    <!-- /feedback-container -->
    <!-- /feedback-container -->
    <!-- ==== END FEEDBACK ==== -->



    <!-- Sản phẩm liên quan -->
    <div class="Related_products">
        <h2 class="title_related_products">SẢN PHẨM LIÊN QUAN</h2>

        <?php if (!empty($relatedProducts)): ?>
        <div class="products_home">
            <!-- Tận dụng luôn class products_home để hiển thị thành grid giống phần đầu -->
            <?php foreach ($relatedProducts as $rp): 
            $rpId   = intval($rp['id']);
            $rpName = htmlspecialchars($rp['name']);
            $rpImg  = !empty($rp['imgURL_1']) ? htmlspecialchars($rp['imgURL_1']) : '/PIXCAM/view/img/default-product.png';
        ?>
            <div class="item_products_home">
                <!-- Dùng class giống sản phẩm chính để đồng bộ giao diện -->
                <div class="image_home_item">
                    <a href="index.php?controller=detailProducts&action=index&id=<?php echo $rpId; ?>">
                        <img src="<?php echo $rpImg; ?>" alt="<?php echo $rpName; ?>" class="image_products_home" />
                    </a>
                </div>

                <!-- Tên sản phẩm -->
                <h4 class="infProducts_home">
                    <a href="index.php?controller=detailProducts&action=index&id=<?php echo $rpId; ?>"
                        style="color: inherit; text-decoration: none;">
                        <?php echo $rpName; ?>
                    </a>
                </h4>

                <!-- Giá sản phẩm -->
                <p class="infProducts_home">
                    <?php if (isset($rp['price_sale'])): ?>
                    <span style="text-decoration: line-through; color: #999;">
                        <?php echo $fm->formatCurrency($rp['price']); ?>
                    </span>
                    &nbsp;
                    <span style="color: #e74c3c; font-weight: bold;">
                        <?php echo $fm->formatCurrency($rp['price_sale']); ?>
                    </span>
                    &nbsp;
                    <span
                        style="background-color: #ff4d4f; color: #fff; padding: 2px 6px; font-size:0.9rem; border-radius:3px;">
                        −<?php echo intval($rp['discount_percent']); ?>%
                    </span>
                    <?php else: ?>
                    <?php echo $fm->formatCurrency($rp['price']); ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div> <!-- end .products_home -->
        <?php
// … footer …
?>

        <?php 
    // Nếu có đủ 4 item trở lên thì hiển thị nút XEM THÊM
    if (count($relatedProducts) >= 4):
        $subId = intval($product['subCategory_id']);
    ?>
        <div class="view-more-box" style="text-align: center; margin-top: 20px;">
            <a href="index.php?controller=Sale&action=index&sub_id=<?php echo $subId; ?>" class="btn_view_more"
                style="display: inline-block; padding: 10px 20px; background-color: #1890ff; color: #fff; border-radius: 4px; text-decoration: none;">
                XEM THÊM
            </a>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <p style="text-align: center; padding: 20px;">Không có sản phẩm liên quan</p>
        <?php endif; ?>
    </div>
</div>


<?php
include 'inc/footer.php';
?>

<script>
// Hàm thay đổi số lượng (chỉ cập nhật input, không cần thay href nữa)
function changeQty(delta) {
    let input = document.getElementById('input-qty');
    let current = parseInt(input.value);
    let next = current + delta;
    if (next < 1) next = 1;
    input.value = next;
}
////////////////////////////
document.getElementById('form-add-cart').addEventListener('submit', function(e) {
    e.preventDefault();

    // Gán color
    const colors = document.getElementsByName('selected_color');
    let selColor = '';
    for (let c of colors) {
        if (c.checked) {
            selColor = c.value;
            break;
        }
    }
    if (!selColor) {
        alert('Vui lòng chọn màu sắc!');
        return;
    }
    document.getElementById('input-color').value = selColor;

    // Gán size nếu có
    const sizes = document.getElementsByName('selected_size');
    if (sizes.length) {
        let selSize = '';
        for (let s of sizes) {
            if (s.checked) {
                selSize = s.value;
                break;
            }
        }
        if (!selSize) {
            alert('Vui lòng chọn kích thước!');
            return;
        }
        document.getElementById('input-size').value = selSize;
    }

    // Gán qty
    document.getElementById('input-qty-hidden').value = document.getElementById('input-qty').value;

    // Gửi AJAX
    const form = e.target;
    fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new FormData(form)
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                // Hiển thị alert thay vì div#cart-alert
                alert(json.message); // => "Sản phẩm đã được thêm vào giỏ hàng"
            } else {
                alert(json.message || 'Thêm giỏ hàng thất bại');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối!');
        });
});
</script>
<script>
function enableAjaxPagination() {
    document.querySelectorAll('.feedback-pagination a').forEach(a => a.onclick = e => {
        e.preventDefault();
        fetch(a.href)
            .then(r => r.text())
            .then(html => {
                const newDom = new DOMParser().parseFromString(html, 'text/html');
                document.querySelector('.feedback-container').innerHTML =
                    newDom.querySelector('.feedback-container').innerHTML;
                enableAjaxPagination(); // gắn lại sự kiện sau khi load trang mới
            });
    });
}
enableAjaxPagination();
</script>