       <div class="filter_shopAlll">
           <?php $total = count($productsArr); ?>
           <p>Hiển thị 1–<?= $total ?> của <?= $total ?> kết quả</p>

           <!-- BẮT ĐẦU: Form chọn sort -->
           <form id="sortForm" method="get" action="index.php">
               <input type="hidden" name="controller" value="<?= htmlspecialchars($_GET['controller'] ?? 'men') ?>">
               <input type="hidden" name="action" value="<?= htmlspecialchars($_GET['action'] ?? 'index') ?>">

               <?php if (isset($_GET['sub_id'])): ?>
               <input type="hidden" name="sub_id" value="<?= intval($_GET['sub_id']) ?>">
               <?php elseif (isset($_GET['cat_id'])): ?>
               <input type="hidden" name="cat_id" value="<?= intval($_GET['cat_id']) ?>">
               <?php endif; ?>

               <input type="hidden" name="sort" id="sortInput" value="<?= htmlspecialchars($_GET['sort'] ?? '') ?>">

               <div class="custom-dropdown" id="customDropdown">
                   <div class="selected" id="selectedText">
                       <?= match ($_GET['sort'] ?? '') {
                'price_asc' => 'Giá: thấp → cao',
                'price_desc' => 'Giá: cao → thấp',
                'latest' => 'Mới nhất',
                default => 'Thứ tự mặc định',
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


           <!-- KẾT THÚC: Form sort -->
       </div>
       </div>

       <?php if (empty($productsArr)): ?>
       <p style="text-align:center; padding: 20px;">
           Chưa có sản phẩm nào cho danh mục này.
       </p>
       <?php else: ?>
       <div class="product_top">
           <div class="products_home">
               <?php
                            $count = 0;
                            foreach ($productsArr as $product):
                                $count++;
                        ?>
               <div class="item_products_home">
                   <div class="image_home_item">
                       <a
                           href="index.php?controller=detailProducts&action=index&id=<?= htmlspecialchars($product['id']) ?>">
                           <?php if (!empty($product['imgURL_1'])): ?>
                           <img src="<?= htmlspecialchars($product['imgURL_1']) ?>"
                               alt="<?= htmlspecialchars($product['name']) ?>" class="image_products_home" />
                           <?php else: ?>
                           <img src="/PIXCAM/view/img/default-product.png" alt="No Image" class="image_products_home" />
                           <?php endif; ?>
                       </a>
                   </div>
                   <h4 class="infProducts_home">
                       <?= htmlspecialchars($product['name']) ?>
                   </h4>
                   <p class="infProducts_home">
                       <?= $fm->formatCurrency($product['price']) ?>
                   </p>
               </div>

               <?php if ($count % 4 === 0 && $count < $total): ?>
           </div>
           <div class="products_home">
               <?php endif; ?>

               <?php endforeach; ?>
           </div>
       </div>
       <?php endif; ?>