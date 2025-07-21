<?php include 'inc/header.php'; ?>

<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700&display=swap" rel="stylesheet">
<link href="view/css/createProduct.css" rel="stylesheet" />

<div class="wrapper">
    <div class="main-content">
        <h2>Thêm sản phẩm</h2>
        <form method="post" action="index.php?controller=admin&action=store" class="product-form">
            <input name="name" placeholder="Tên sản phẩm" required>
            <input name="price" type="number" placeholder="Giá" required>
            <input name="quantity" type="number" placeholder="Số lượng" required>
            <textarea name="detail" placeholder="Chi tiết sản phẩm..." rows="4"></textarea>

            <input name="imgURL_1" placeholder="URL ảnh 1">
            <input name="imgURL_2" placeholder="URL ảnh 2">
            <input name="imgURL_3" placeholder="URL ảnh 3">
            <input name="imgURL_4" placeholder="URL ảnh 4">

            <label>Danh mục con:</label>
            <select name="subCategory_id" id="subCategorySelect" required>
                <option value="">-- Chọn danh mục --</option>
                <?php foreach ($subCategories as $sub): ?>
                <option value="<?= $sub['id'] ?>" data-name="<?= $sub['name'] ?>"><?= htmlspecialchars($sub['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>

            <label>Khuyến mãi:</label>
            <select id="saleSelectDisplay" disabled>
                <option value="">-- Không áp dụng --</option>
                <?php foreach ($sales as $sale): ?>
                <option value="<?= $sale['id'] ?>" data-name="<?= $sale['name'] ?>">
                    <?= htmlspecialchars($sale['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="Sale_id" id="saleIdHidden">

            <!-- Màu sắc -->
            <div>
                <label>Màu sắc:</label>
                <div id="colorFields">
                    <div class="field-group">
                        <input type="text" name="colors[]" placeholder="Màu sắc">
                        <button type="button" onclick="removeField(this)">❌</button>
                    </div>
                </div>
                <button type="button" class="add-btn" onclick="addColorField()">+ Thêm màu</button>
            </div>

            <!-- Kích thước -->
            <div>
                <label>Kích thước:</label>
                <div id="sizeFields">
                    <div class="field-group">
                        <select name="sizes[]" class="size-select">
                            <option value="">-- Chọn size --</option>
                            <?php foreach ($sizes as $size): ?>
                            <option value="<?= $size['id'] ?>"><?= htmlspecialchars($size['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" onclick="removeSizeField(this)">❌</button>
                    </div>
                </div>
                <button type="button" class="add-btn" onclick="addSizeField()">+ Thêm size</button>
            </div>

            <button type="submit">Thêm sản phẩm</button>
            <a id="back-btn" href="index.php?controller=admin&action=index" class="btn back-btn">Quay lại</a>
        </form>
    </div>
</div>
<?php include 'inc/footer.php'; ?>

<!-- Truyền size từ PHP sang JS -->
<script>
const sizeOptions = <?= json_encode($sizes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
</script>

<script>
document.getElementById('subCategorySelect').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const subName = selectedOption.getAttribute('data-name') || '';
    const saleSelectDisplay = document.getElementById('saleSelectDisplay');
    const saleIdHidden = document.getElementById('saleIdHidden');
    let matched = false;

    for (let i = 0; i < saleSelectDisplay.options.length; i++) {
        const saleOption = saleSelectDisplay.options[i];
        const saleName = saleOption.getAttribute('data-name') || '';

        if (
            (subName.includes("30") && saleName.includes("30")) ||
            (subName.includes("50") && saleName.includes("50")) ||
            (subName.includes("70") && saleName.includes("70"))
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
});

function addColorField() {
    const div = document.createElement('div');
    div.className = 'field-group';
    div.innerHTML = `
        <input type="text" name="colors[]" placeholder="Màu sắc">
        <button type="button" onclick="removeField(this)">❌</button>
    `;
    document.getElementById('colorFields').appendChild(div);
}

function removeField(btn) {
    btn.parentNode.remove();
}

function removeSizeField(btn) {
    btn.parentNode.remove();
    updateSizeOptions();
}

function addSizeField() {
    const div = document.createElement('div');
    div.className = 'field-group';

    const select = document.createElement('select');
    select.name = "sizes[]";
    select.className = "size-select";
    select.addEventListener('change', updateSizeOptions);

    div.appendChild(select);

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.textContent = '❌';
    removeBtn.addEventListener('click', () => {
        div.remove();
        updateSizeOptions(); // cập nhật lại sau khi xóa
    });

    div.appendChild(removeBtn);
    document.getElementById('sizeFields').appendChild(div);

    updateSizeOptions(); // cập nhật option ngay sau khi thêm
}

// Cập nhật tất cả select để tránh trùng size
function updateSizeOptions() {
    const selects = document.querySelectorAll('.size-select');
    const selectedValues = Array.from(selects).map(s => s.value).filter(v => v !== "");

    selects.forEach(select => {
        const currentValue = select.value;

        select.innerHTML = ''; // clear all options

        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Chọn size --';
        select.appendChild(defaultOption);

        sizeOptions.forEach(size => {
            const isUsedElsewhere =
                selectedValues.includes(String(size.id)) &&
                String(size.id) !== currentValue;

            if (!isUsedElsewhere || String(size.id) === currentValue) {
                const option = document.createElement('option');
                option.value = size.id;
                option.textContent = size.name;
                if (String(size.id) === currentValue) {
                    option.selected = true;
                }
                select.appendChild(option);
            }
        });
    });
}


// Khi trang load
document.addEventListener('DOMContentLoaded', () => {
    updateSizeOptions();
});
</script>