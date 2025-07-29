 <?php include 'inc/header.php'; ?>
 <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700&display=swap" rel="stylesheet">

 <link href="<?php echo BASE_URL; ?>view/css/admin.css" rel="stylesheet" />
 <div class="wrapper">


     <main>
         <h2>Danh sách sản phẩm</h2>

         <div class="action-bar">

             <a id="Themsanpham" href="index.php?controller=admin&action=create" class="btn add-btn">➕ Thêm sản
                 phẩm</a>
         </div>

         <form method="post" action="index.php?controller=admin&action=deleteMulti"
             onsubmit="return confirm('Bạn có chắc muốn xóa các sản phẩm đã chọn?');">
             <table>
                 <thead>
                     <tr>
                         <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll(this)"></th>
                         <th>ID</th>
                         <th>Tên</th>
                         <th>Giá</th>
                         <th>Số lượng</th>
                         <th>Chi tiết</th>
                         <th>Hành động</th>
                     </tr>
                 </thead>
                 <tbody>
                     <?php foreach ($products as $product): ?>
                     <tr>
                         <td><input type="checkbox" name="ids[]" value="<?= $product['id'] ?>"></td>
                         <td><?= $product['id'] ?></td>
                         <td class="tooltip" data-tooltip="<?= htmlspecialchars($product['name']) ?>">
                             <?= htmlspecialchars($product['name']) ?>
                         </td>
                         <td><?= $product['price'] ?></td>
                         <td><?= $product['quantity'] ?></td>
                         <td><?= htmlspecialchars($product['detail']) ?></td>
                         <td>
                             <a href="javascript:void(0)" class="action-link"
                                 onclick="openModal(<?= $product['id'] ?>)">✏️ Sửa</a>
                         </td>
                     </tr>
                     <?php endforeach; ?>
                 </tbody>
             </table>
             <button class="btn-deleteproduct" type="submit">🗑️ Xóa đã chọn</button>
         </form>
         <!-- PHẦN PHÂN TRANG -->
         <?php if (isset($totalPages) && $totalPages > 1): ?>
         <nav class="admin-pagination">
             <!-- Prev -->
             <?php if ($page > 1): ?>
             <a href="?controller=admin&action=index&page=<?= $page-1 ?>" class="admin-page-link">«</a>
             <?php else: ?>
             <span class="admin-page-link admin-disabled">«</span>
             <?php endif; ?>

             <?php
            $start = max(1, $page - 2);
            $end   = min($totalPages, $page + 2);

            if ($start > 1) {
                echo '<a href="?controller=admin&action=index&page=1" class="admin-page-link">1</a>';
                if ($start > 2) echo '<span class="admin-dots">…</span>';
            }

            for ($i = $start; $i <= $end; $i++):
            ?>
             <?php if ($i == $page): ?>
             <span class="admin-page-link admin-current"><?= $i ?></span>
             <?php else: ?>
             <a href="?controller=admin&action=index&page=<?= $i ?>" class="admin-page-link"><?= $i ?></a>
             <?php endif; ?>
             <?php endfor; ?>

             <?php if ($end < $totalPages): ?>
             <?php if ($end < $totalPages - 1) echo '<span class="admin-dots">…</span>'; ?>
             <a href="?controller=admin&action=index&page=<?= $totalPages ?>"
                 class="admin-page-link"><?= $totalPages ?></a>
             <?php endif; ?>

             <!-- Next -->
             <?php if ($page < $totalPages): ?>
             <a href="?controller=admin&action=index&page=<?= $page+1 ?>" class="admin-page-link">»</a>
             <?php else: ?>
             <span class="admin-page-link admin-disabled">»</span>
             <?php endif; ?>
         </nav>
         <?php endif; ?>
         <!-- KẾT THÚC PHÂN TRANG -->
         <!-- Modal Popup -->
         <div class="modal" id="editModal">
             <div class="modal-content">
                 <h3>Xác nhận sửa sản phẩm?</h3>
                 <button class="confirm-edit" id="confirmEdit">Đồng ý</button>
                 <button class="close-modal" onclick="closeModal()">Huỷ</button>
             </div>
         </div>
     </main>


 </div>
 <?php include 'inc/footer.php'; ?>
 <script>
function toggleSelectAll(source) {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

let selectedEditId = null;

function openModal(id) {
    selectedEditId = id;
    document.getElementById("editModal").style.display = "block";
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
    selectedEditId = null;
}

document.getElementById("confirmEdit").addEventListener("click", function() {
    if (selectedEditId !== null) {
        window.location.href = "index.php?controller=admin&action=edit&id=" + selectedEditId;
    }
});

window.onclick = function(event) {
    const modal = document.getElementById("editModal");
    if (event.target == modal) closeModal();
}
 </script>