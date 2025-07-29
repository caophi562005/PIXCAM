<?php

// Lấy mảng category-tree từ biến global
$cats = isset($GLOBALS['global_categories_tree']) ? $GLOBALS['global_categories_tree'] : [];

/**
 * Trả về controller dựa trên id của category; nếu không tìm thấy, trả về 'home'.
 */
function getControllerForId($catId) {
    switch ((int)$catId) {
        case 1: return 'men';
        case 2: return 'women';
        case 3: return 'accessories';
        case 4: return 'sale';
        default: return 'home';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PIXCAM</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/home.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/accesPay.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/Cart.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/CSTV.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/Register.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/PayCart.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/Sale.css" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/css/Top.css" />
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>view/img/home/logo.png" />

    <style>
    #accountMenu a {
        color: #000;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 6px;
        transition: all 0.25s ease;
        width: 93%;
    }

    #accountMenu a:hover {
        color: #ee5022;
        background-color: rgba(238, 80, 34, 0.1);
        /* Màu cam nhạt khi hover */
        transform: translateX(6px);
    }
    </style>
</head>

<body>
    <header>
        <div class="logo_header">
            <a href="index.php?controller=home&action=index" class="logo">
                <img src="<?php echo BASE_URL; ?>view/img/home/logo.png" alt="Logo">
            </a>
        </div>

        <!-- ==============================================================
             Phần menu chính (danh sách category + subcategory)
             ============================================================== -->
        <ul class="navigate_header">
            <li><a href="index.php?controller=home&action=index" class="title_header">HOME</a></li>
            <?php foreach ($cats as $cat):
                // Lấy controller dựa trên id
                $ctrl = getControllerForId($cat['id']);
                $catTitle = mb_strtoupper($cat['name'], 'UTF-8');
            ?>
            <li class="dropdown_header">
                <a href="index.php?controller=<?= $ctrl ?>&action=index&cat_id=<?= $cat['id']; ?>" class="title_header">
                    <?= htmlspecialchars($catTitle) ?>
                </a>

                <?php if (!empty($cat['subcategories'])): ?>
                <div class="mega_menu">
                    <div class="column">
                        <?php foreach ($cat['subcategories'] as $sub):
                            // Subcategory giữ nguyên đúng tên từ CSDL
                            $subTitle = $sub['name'];
                        ?>
                        <a href="index.php?controller=<?= $ctrl ?>&action=index&sub_id=<?= $sub['id']; ?>">
                            <?= htmlspecialchars($subTitle) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <!-- ==============================================================
             Phần công cụ bên phải: Đăng ký, giỏ hàng, tìm kiếm
             ============================================================== -->
        <ul class="tools_header">
            <li class="account-menu" style="position: relative; display: inline-block;">
                <div class="menu-button" onclick="toggleAccountMenu()" style="cursor: pointer;">
                    <i class="fa-solid fa-user icon_while"></i>
                </div>
                <div id="accountMenu" style="
        display: none;
        position: absolute;
        top: 130%;
        right: 0;
        background-color: #fef6e4;
        opacity: 1;
        min-width: 180px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        border-radius: 6px;
        z-index: 999;
    ">
                    <?php 
        
        if (!isset($_SESSION['username'])): ?>
                    <a href="index.php?controller=login&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Đăng nhập</a>
                    <a href="index.php?controller=register&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Đăng ký</a>
                    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href=" index.php?controller=account&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Quản lý tài khoản</a>
                    <a href=" index.php?controller=profile&action=edit" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Thông tin tài khoản</a>
                    <a href="index.php?controller=cart&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Giỏ hàng</a>
                    <a href="index.php?controller=order&action=history" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Lịch sử mua hàng</a>
                    <a href="index.php?controller=admin&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Quản lý sản phẩm</a>
                    <a href="index.php?controller=revenue&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Thống kê doanh thu</a>
                    <a href="index.php?controller=CouponAdmin&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Quản lý mã giảm giá</a>
                    <a href="index.php?controller=login&action=logout" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Đăng xuất</a>
                    <?php else: ?>

                    <a href=" index.php?controller=profile&action=edit" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Thông tin tài khoản</a>
                    <a href="index.php?controller=cart&action=index" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Giỏ hàng</a>
                    <a href="index.php?controller=order&action=history" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Lịch sử mua hàng</a>
                    <a href="index.php?controller=login&action=logout" style="
                display: block;
                padding: 10px 15px;
                
                text-decoration: none;
                font-size: 14px;
            ">Đăng xuất</a>

                    <?php endif; ?>
                </div>
            </li>


            <!-- Thay vì chỉ hiển thị icon, ta sẽ đưa cả form tìm kiếm vào đây -->
            <li class="header_search_wrapper">
                <form method="get" action="index.php" class="header_search_form">
                    <input type="hidden" name="controller" value="FindProduct" />
                    <input type="hidden" name="action" value="index" />
                    <input type="text" name="q" class="header_search_input" placeholder="Tìm sản phẩm..."
                        value="<?= isset($_GET['q']) ? htmlspecialchars(trim($_GET['q'])) : '' ?>" autocomplete="off" />
                    <button type="submit" class="header_search_btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </li>

        </ul>
    </header>