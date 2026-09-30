<?php
require __DIR__ . '/data.php';
require __DIR__ . '/functions.php';

$catalog = load_catalog();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$categories = unique_categories($catalog);
$errors = array();
$old = array('title' => '', 'category' => '', 'price' => '', 'stock' => '', 'discount' => '0');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old = $_POST;
    $errors = validate_product($_POST);
    if (empty($errors)) {
        $newProduct = sanitize_product($_POST);
        $newProduct['id'] = next_id($catalog);
        $catalog[] = $newProduct;
        save_catalog($catalog);
        header('Location: index.php?added=1');
        exit;
    }
}

$products = search_by_name($catalog, $search);
$products = filter_by_category($products, $category);
$productsLength = count($products);
$stats = catalog_stats($catalog);
$fileSize = filesize(CATALOG_FILE);
$fileDate = date('d.m.Y H:i:s', filemtime(CATALOG_FILE));
?>
<!DOCTYPE html>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог товаров</title>
</head>
<body>

<h1>Каталог товаров</h1>

<p>
    Товаров: <?= e($stats['products']) ?>,
    остаток: <?= e($stats['stock']) ?>,
    стоимость склада: <?= e($stats['value']) ?> руб.
</p>

<?php if (isset($_GET['added'])): ?>
    <p>Товар добавлен</p>
<?php endif; ?>

<form method="get">
    <p>
        <label for="search">Поиск:</label>
        <input type="text" id="search" name="search" value="<?= e($search) ?>">
    </p>
    <p>
        <label for="category">Категория:</label>
        <select id="category" name="category">
            <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>Все</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= e($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <button type="submit">Применить</button>
</form>

<hr>

<h2>Таблица товаров</h2>

<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Категория</th>
        <th>Цена</th>
        <th>Скидка</th>
        <th>Остаток</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($productsLength == 0): ?>
        <tr><td colspan="6">Ничего не найдено</td></tr>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= e($p['id']) ?></td>
                <td><?= e($p['title']) ?></td>
                <td><?= e($p['category']) ?></td>
                <td><?= e($p['price']) ?></td>
                <td><?= e($p['discount']) ?>%</td>
                <td><?= e($p['stock']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<hr>

<h2>Добавить товар</h2>

<form method="post" novalidate>
    <p>
        <label for="title">Название:</label>
        <input type="text" id="title" name="title" value="<?= e(isset($old['title']) ? $old['title'] : '') ?>">
        <?php if (isset($errors['title'])): ?>
            <span><?= e($errors['title']) ?></span>
        <?php endif; ?>
    </p>

    <p>
        <label for="new_category">Категория:</label>
        <select id="new_category" name="category">
            <option value="">— выберите —</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>"
                        <?= (isset($old['category']) && $old['category'] === $cat) ? 'selected' : '' ?>>
                    <?= e($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['category'])): ?>
            <span><?= e($errors['category']) ?></span>
        <?php endif; ?>
    </p>

<p>
    <label for="price">Цена:</label>
    <input type="text" id="price" name="price" value="<?= e(isset($old['price']) ? $old['price'] : '') ?>">
    <?php if (isset($errors['price'])): ?>
        <span><?= e($errors['price']) ?></span>
    <?php endif; ?>
</p>

<p>
    <label for="stock">Остаток:</label>
    <input type="text" id="stock" name="stock" value="<?= e(isset($old['stock']) ? $old['stock'] : '') ?>">
    <?php if (isset($errors['stock'])): ?>
        <span><?= e($errors['stock']) ?></span>
    <?php endif; ?>
</p>

<p>
    <label for="discount">Скидка (%):</label>
    <input type="text" id="discount" name="discount" value="<?= e(isset($old['discount']) ? $old['discount'] : '') ?>">
    <?php if (isset($errors['discount'])): ?>
        <span><?= e($errors['discount']) ?></span>
    <?php endif; ?>
</p>

<button type="submit">Добавить</button>
</form>

<hr>

<p>
    Файл: <?= e(CATALOG_FILE) ?>,
    размер: <?= e($fileSize) ?> байт,
    изменён: <?= e($fileDate) ?>
</p>

</body>
</html>