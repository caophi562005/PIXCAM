<?php include 'inc/header.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700&display=swap" rel="stylesheet">
<link href="<?php echo BASE_URL; ?>view/css/editProduct.css" rel="stylesheet" />


<div class="wrapper">
    <main class="main-content">
        <h2>Cập nhật sản phẩm</h2>

        <form method="post" action="index.php?controller=admin&action=update" class="product-form">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <input name="name" placeholder="Tên sản phẩm" value="<?= htmlspecialchars($product['name']) ?>" required>
            <input name="price" type="number" placeholder="Giá" value="<?= $product['price'] ?>" required>
            <input name="quantity" type="number" placeholder="Số lượng" value="<?= $product['quantity'] ?>" required>
            <textarea name="detail" placeholder="Chi tiết sản phẩm..."
                rows="4"><?= htmlspecialchars($product['detail']) ?></textarea>
            <input name="imgURL_1" placeholder="URL ảnh 1" value="<?= $product['imgURL_1'] ?>">
            <input name="imgURL_2" placeholder="URL ảnh 2" value="<?= $product['imgURL_2'] ?>">
            <input name="imgURL_3" placeholder="URL ảnh 3" value="<?= $product['imgURL_3'] ?>">
            <input name="imgURL_4" placeholder="URL ảnh 4" value="<?= $product['imgURL_4'] ?>">

            <label>Danh mục con:</label>
            <select name="subCategory_id" id="subCategorySelect" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($subCategories as $sub): ?>
                <option value="<?= $sub['id'] ?>" data-name="<?= $sub['name'] ?>"
                    <?= $sub['id'] == $product['subCategory_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sub['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Khuyến mãi:</label>
            <select id="saleSelectDisplay" disabled>
                <option value="">-- Không áp dụng --</option>
                <?php foreach ($sales as $sale): ?>
                <option value="<?= $sale['id'] ?>" data-name="<?= $sale['name'] ?>"
                    <?= $sale['id'] == $product['Sale_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sale['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="Sale_id" id="saleIdHidden" value="<?= $product['Sale_id'] ?>">

            <!-- Màu sắc -->
            <div>
                <label>Màu sắc:</label>
                <div id="colorFields">
                    <?php foreach ($colors as $color): ?>
                    <div class="field-group">
                        <input type="text" name="colors[]" value="<?= htmlspecialchars($color['colorName']) ?>"
                            placeholder="Màu sắc">
                        <button type="button" onclick="removeField(this)">❌</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="addColorField()">+ Thêm màu</button>
            </div>

            <!-- Kích thước -->
            <div>
                <label>Kích thước:</label>
                <div id="sizeFields">
                    <?php foreach ($selectedSizes as $selectedSizeId): ?>
                    <div class="field-group">
                        <select name="sizes[]" class="size-select">
                            <option value="">-- Chọn size --</option>
                            <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size['id'] ?>"
                                <?= $size['id'] == $selectedSizeId['size_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($size['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="removeSizeField(this)">❌</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" onclick="addSizeField()">+ Thêm size</button>
            </div>

            <button type="submit">Cập nhật sản phẩm</button>
            <a id="back-btn" href="index.php?controller=admin&action=index">Quay lại</a>
        </form>
    </main>
</div>

<?php include 'inc/footer.php'; ?>



<script>
const sizeOptions = <?= json_encode($sizes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

document.addEventListener("DOMContentLoaded", function() {
    const subCategorySelect = document.getElementById("subCategorySelect");
    const saleSelectDisplay = document.getElementById("saleSelectDisplay");
    const saleIdHidden = document.getElementById("saleIdHidden");

    function updateSaleId() {
        const selectedOption = subCategorySelect.options[subCategorySelect.selectedIndex];
        const subName = selectedOption.getAttribute('data-name') || '';
        let matched = false;
        for (let i = 0; i < saleSelectDisplay.options.length; i++) {
            const saleOption = saleSelectDisplay.options[i];
            const saleName = saleOption.getAttribute('data-name') || '';
            if (
                (subName.toLowerCase().includes("30") && saleName.includes("30")) ||
                (subName.toLowerCase().includes("50") && saleName.includes("50")) ||
                (subName.toLowerCase().includes("70") && saleName.includes("70"))
            ) {
                saleSelectDisplay.selectedIndex = i;
                saleIdHidden.value = saleOption.value;
                matched = true;
                break;
            }
        }
        if (!matched) {
            saleSelectDisplay.selectedIndex = 0;
            saleIdHidden.value = "";
        }
    }
    updateSaleId();
    subCategorySelect.addEventListener("change", updateSaleId);
});

function removeField(btn) {
    btn.parentNode.remove();
}

function removeSizeField(btn) {
    btn.parentNode.remove();
    updateSizeOptions();
}

function addColorField() {
    const div = document.createElement('div');
    div.className = 'field-group';
    div.innerHTML =
        `<input type="text" name="colors[]" placeholder="Màu sắc"><button type="button" onclick="removeField(this)">❌</button>`;
    document.getElementById('colorFields').appendChild(div);
}

function addSizeField() {
    const selectedSizeIds = Array.from(document.querySelectorAll('.size-select')).map(s => s.value).filter(v => v !==
        "");
    if (selectedSizeIds.length >= sizeOptions.length) {
        alert("Bạn đã chọn đủ tất cả các size. Không thể thêm size mới.");
        return;
    }

    const div = document.createElement('div');
    div.className = 'field-group';

    const select = document.createElement('select');
    select.name = "sizes[]";
    select.className = "size-select";
    select.addEventListener('change', updateSizeOptions);
    select.innerHTML = `<option value="">-- Chọn size --</option>`;

    sizeOptions.forEach(size => {
        if (!selectedSizeIds.includes(String(size.id))) {
            const option = document.createElement('option');
            option.value = size.id;
            option.textContent = size.name;
            select.appendChild(option);
        }
    });

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.textContent = '❌';
    removeBtn.onclick = () => {
        div.remove();
        updateSizeOptions();
    };

    div.appendChild(select);
    div.appendChild(removeBtn);
    document.getElementById('sizeFields').appendChild(div);
    updateSizeOptions();
}

function updateSizeOptions() {
    const selects = document.querySelectorAll('.size-select');
    const selectedValues = Array.from(selects).map(s => s.value).filter(v => v !== "");
    selects.forEach(select => {
        const currentValue = select.value;
        select.innerHTML = `<option value="">-- Chọn size --</option>`;
        sizeOptions.forEach(size => {
            const isUsedElsewhere = selectedValues.includes(String(size.id)) && String(size.id) !==
                currentValue;
            if (!isUsedElsewhere || String(size.id) === currentValue) {
                const option = document.createElement('option');
                option.value = size.id;
                option.textContent = size.name;
                if (String(size.id) === currentValue) option.selected = true;
                select.appendChild(option);
            }
        });
    });
}
document.addEventListener('DOMContentLoaded', () => updateSizeOptions());
</script>