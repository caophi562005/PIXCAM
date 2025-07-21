<?php
include 'inc/header.php';
include_once 'helpers/format.php';
$fm = new Format();

// Controller đã truyền vào 3 biến: 
//  - $keyword: từ khóa tìm kiếm ($_GET['q'])
//  - $sort: giá trị sort ($_GET['sort'])
//  - $productsArr: mảng kết quả tìm được (có thể rỗng)
?>

<style>
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.pagination a,
.pagination span {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 38px;
    height: 38px;
    font-size: 16px;
    border-radius: 6px;
    text-decoration: none;
    background-color: #2c2c2c;
    color: #ccc;
    transition: all 0.25s ease;
    border: 1px solid #444;
}

.pagination a:hover {
    background-color: #ff6f00;
    color: #fff;
    border-color: #ff6f00;
}

.pagination .active {
    background-color: #ff6f00;
    color: white;
    font-weight: bold;
    border-color: #ff6f00;
}

.pagination .disabled {
    opacity: 0.4;
    pointer-events: none;
    background-color: #1e1e1e;
    border-color: #333;
}
</style>


<main>
    <div class="content">
        <div class="content_top">

            <!-- =========================
                 BREADCRUMB + FILTER (ĐƯA CHUNG VỀ 1 HÀNG)
            ========================= -->
            <div class="contentProducts_navigate">
                <!-- Breadcrumb -->
                <div class="navigate_shopAll">
                    <p class="title_navigate">
                        <span class="home_navigate">TRANG CHỦ</span> / TÌM KIẾM
                    </p>
                </div>

                <!-- Filter + Sort -->
                <div class="filter_shopAlll">
                    <?php $total = count($productsArr); ?>
                    <p>Hiển thị 1–<?= $total ?> của <?= $total ?> kết quả</p>

                    <!-- FORM CHỌN SORT (lưu giữ từ khóa q trong input hidden) -->
                    <form id="sortForm" method="get" action="index.php">
                        <input type="hidden" name="controller" value="FindProduct">
                        <input type="hidden" name="action" value="index">
                        <input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>">
                        <input type="hidden" name="sort" id="sortInput" value="<?= htmlspecialchars($sort) ?>">

                        <div class="custom-dropdown" id="customDropdown">
                            <div class="selected" id="selectedText">
                                <?= match ($sort) {
                                    'price_asc'  => 'Giá: thấp → cao',
                                    'price_desc' => 'Giá: cao → thấp',
                                    'latest'     => 'Mới nhất',
                                    default      => 'Thứ tự mặc định',
                                } ?> &#9662;
                            </div>
                            <ul class="options">
                                <li data-value="">Thứ tự mặc định</li>
                                <li data-value="price_asc">Giá: thấp → cao</li>
                                <li data-value="price_desc">Giá: cao → thấp</li>
                                <li data-value="latest">Mới nhất</li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Breadcrumb + Filter -->

            <!-- Trường hợp người ta chưa nhập từ khóa -->
            <?php if ($keyword === ''): ?>
            <p style="text-align:center; padding: 20px;">Vui lòng nhập từ khóa tìm kiếm.</p>
            <div style="height: 300px;"></div>
            <!-- Trường hợp đã nhập nhưng không tìm thấy -->
            <?php elseif (empty($productsArr)): ?>
            <p style="text-align:center; padding: 20px;">
                Không tìm thấy sản phẩm nào chứa “<strong><?= htmlspecialchars($keyword) ?></strong>”.
            </p>
            <div style="height: 300px;"></div>
            <!-- Trường hợp có kết quả -->
            <?php else: ?>
            <div class="product_top">
                <?php
                    $perRow = 4;
                    $count = 0;
                    $totalProducts = count($productsArr);

                    foreach ($productsArr as $product):
                        // Mở .products_home khi bắt đầu hàng mới
                        if ($count % $perRow === 0):
                ?>
                <div class="products_home">
                    <?php
                        endif;

                        $count++;
                        $id     = intval($product['id']);
                        $name   = htmlspecialchars($product['name']);
                        $price  = floatval($product['price'] ?? 0);
                        $imgURL = !empty($product['imgURL_1'])
                                  ? htmlspecialchars($product['imgURL_1'])
                                  : '/PIXCAM/view/img/default-product.png';

                        // Giá sale và phần trăm giảm do model đã tính:
                        $hasSale = isset($product['price_sale']) && floatval($product['price_sale']) > 0;
                        $priceDisplay    = $fm->formatCurrency($price);
                        $priceSaleDisplay = $hasSale
                                            ? $fm->formatCurrency(floatval($product['price_sale']))
                                            : '';
                        $discountPercent = $hasSale
                                            ? intval($product['discount_percent'])
                                            : 0;
                ?>
                    <div class="item_products_home">
                        <div class="image_home_item">
                            <?php if ($hasSale): ?>
                            <div class="product_sale">
                                <p class="text_products_sale">−<?= $discountPercent ?>%</p>
                            </div>
                            <?php endif; ?>
                            <a href="index.php?controller=detailProducts&action=index&id=<?= $id ?>">
                                <img src="<?= $imgURL ?>" alt="<?= $name ?>" class="image_products_home" />
                            </a>
                        </div>
                        <h4 class="infProducts_home"><?= $name ?></h4>
                        <p class="infProducts_home">
                            <?php if ($hasSale): ?>
                            <span class="price-original"><?= $priceDisplay ?></span>
                            &nbsp;
                            <span class="price-sale"><?= $priceSaleDisplay ?></span>
                            <?php else: ?>
                            <?= $priceDisplay ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <?php
                        // Đóng .products_home khi đủ 4 item hoặc là cuối mảng
                        if ($count % $perRow === 0 || $count === $totalProducts):
                ?>
                </div> <!-- Đóng .products_home -->
                <?php
                        endif;
                    endforeach;
                ?>
            </div> <!-- Đóng .product_top -->
            <?php endif; ?>

        </div> <!-- Đóng .content_top -->
    </div> <!-- Đóng .content -->
    <!-- BẮT ĐẦU: PHÂN TRANG -->
    <?php if ($totalPages > 1): ?>

    <div class="pagination">
        <?php
    $baseUrl = 'index.php?controller=findProduct&action=index';
    if (!empty($keyword)) {
        $baseUrl .= '&q=' . urlencode($keyword);
    }
    if (!empty($sort)) {
        $baseUrl .= '&sort=' . urlencode($sort);
    }

    $range = 2;

    // Prev
    if ($page > 1) {
        echo '<a href="' . $baseUrl . '&page=' . ($page - 1) . '"><i class="fas fa-angle-left"></i></a>';
    } else {
        echo '<span class="disabled"><i class="fas fa-angle-left"></i></span>';
    }

    // Trang đầu
    if ($page > $range + 2) {
        echo '<a href="' . $baseUrl . '&page=1">1</a>';
        echo '<span class="disabled">...</span>';
    }

    // Các trang giữa
    for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++) {
        if ($i == $page) {
            echo '<span class="active">' . $i . '</span>';
        } else {
            echo '<a href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a>';
        }
    }

    // Trang cuối
    if ($page < $totalPages - ($range + 1)) {
        echo '<span class="disabled">...</span>';
        echo '<a href="' . $baseUrl . '&page=' . $totalPages . '">' . $totalPages . '</a>';
    }

    // Next
    if ($page < $totalPages) {
        echo '<a href="' . $baseUrl . '&page=' . ($page + 1) . '"><i class="fas fa-angle-right"></i></a>';
    } else {
        echo '<span class="disabled"><i class="fas fa-angle-right"></i></span>';
    }
    ?>
    </div>

    <?php endif; ?>


    <!-- KẾT THÚC: PHÂN TRANG -->
</main>


<?php include 'inc/footer.php'; ?>