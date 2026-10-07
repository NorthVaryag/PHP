<?php
require __DIR__ . '/functions.php';
require __DIR__ . '/layout.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$catalog = load_catalog();
$product = find_product($catalog, $id);

if ($product === null) {
    http_response_code(404);
    page_header('Товар не найден');
    echo '<p>Товар не найден</p>';
    page_footer();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catalog = delete_product($catalog, $id);
    save_catalog($catalog);
    header('Location: index.php?deleted=' . $id);
    exit;
}

page_header('Удаление товара');
?>
    <p>Удалить товар?</p>
    <ul>
        <li>ID: <?= e($product['id']) ?></li>
        <li>Название: <?= e($product['title']) ?></li>
        <li>Категория: <?= e($product['category']) ?></li>
        <li>Цена: <?= e($product['price']) ?></li>
        <li>Остаток: <?= e($product['stock']) ?></li>
    </ul>
    <form method="post" action="delete.php?id=<?= e($id) ?>">
        <button type="submit">Да, удалить</button>
        <a href="index.php">Отмена</a>
    </form>
<?php
page_footer();