<?php
require __DIR__ . '/functions.php';
require __DIR__ . '/layout.php';

$catalog = load_catalog();
$categories = unique_categories($catalog);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'price_asc';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if (!in_array($sort, ['price_asc', 'price_desc', 'title', 'stock'])) {
    $sort = 'price_asc';
}

$products = search_by_name($catalog, $search);
$products = filter_by_category($products, $category);

if ($sort == 'price_desc') {
    $products = sort_by_price($products, 'desc');
} elseif ($sort == 'title') {
    $products = sort_by_title($products);
} elseif ($sort == 'stock') {
    $products = sort_by_stock($products, 'desc');
} else {
    $products = sort_by_price($products, 'asc');
}

$pageData = paginate($products, $page, PER_PAGE);
$stats = catalog_stats($catalog);

page_header('Каталог товаров');

if (isset($_GET['added'])) {
    banner('ok', 'Товар #' . (int)$_GET['added'] . ' добавлен');
}
if (isset($_GET['updated'])) {
    banner('ok', 'Товар #' . (int)$_GET['updated'] . ' обновлён');
}
if (isset($_GET['deleted'])) {
    banner('ok', 'Товар #' . (int)$_GET['deleted'] . ' удалён');
}
?>

    <p>
        Товаров: <?= e($stats['products']) ?>,
        остаток: <?= e($stats['stock']) ?>,
        стоимость склада: <?= e(number_format($stats['value'], 2, ',', ' ')) ?> руб.
    </p>

    <form method="get">
        <p>
            <label for="search">Поиск:</label>
            <input type="text" id="search" name="search" value="<?= e($search) ?>">
        </p>
        <p>
            <label for="cat">Категория:</label>
            <select id="cat" name="category">
                <option value="all">Все</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                        <?= e($cat) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label for="sort">Сортировка:</label>
            <select id="sort" name="sort">
                <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Цена ↑</option>
                <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Цена ↓</option>
                <option value="title" <?= $sort == 'title' ? 'selected' : '' ?>>По алфавиту</option>
                <option value="stock" <?= $sort == 'stock' ? 'selected' : '' ?>>По остатку</option>
            </select>
        </p>
        <button type="submit">Применить</button>
    </form>

    <hr>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Цена</th>
            <th>Скидка</th>
            <th>Цена со скидкой</th>
            <th>Наличие</th>
            <th></th>
        </tr>
        <?php if (count($pageData['items']) == 0): ?>
            <tr><td colspan="8">Ничего не найдено</td></tr>
        <?php else: ?>
            <?php foreach ($pageData['items'] as $p): ?>
                <tr>
                    <td><?= e($p['id']) ?></td>
                    <td><?= e($p['title']) ?></td>
                    <td><?= e($p['category']) ?></td>
                    <td><?= e($p['price']) ?></td>
                    <td><?= e($p['discount']) ?>%</td>
                    <td><?= e(number_format(price_with_discount($p['price'], $p['discount']), 2, ',', ' ')) ?></td>
                    <td><?= e(stock_label($p['stock'])) ?> (<?= e($p['stock']) ?>)</td>
                    <td>
                        <a href="edit.php?id=<?= e($p['id']) ?>">Изменить</a>
                        <a href="delete.php?id=<?= e($p['id']) ?>">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <p>
        Страницы:
        <?php for ($i = 1; $i <= $pageData['totalPages']; $i++): ?>
            <?php if ($i == $pageData['page']): ?>
                <b><?= $i ?></b>
            <?php else: ?>
                <a href="<?= e(query_url(['page' => $i])) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </p>
<?php
page_footer();